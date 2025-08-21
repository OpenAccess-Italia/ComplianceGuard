<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\ActionLogController;
use App\Mail\CNCPOReply;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Mail;
use PhpMimeMailParser\Parser;
use Swift_Mailer;
use Swift_SmtpTransport;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\EventNotFoundException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Webklex\PHPIMAP\Exceptions\GetMessagesFailedException;
use Webklex\PHPIMAP\Exceptions\InvalidMessageDateException;
use Webklex\PHPIMAP\Exceptions\InvalidWhereQueryCriteriaException;
use Webklex\PHPIMAP\Exceptions\MaskNotFoundException;
use Webklex\PHPIMAP\Exceptions\MessageContentFetchingException;
use Webklex\PHPIMAP\Exceptions\MessageFlagException;
use Webklex\PHPIMAP\Exceptions\MessageHeaderFetchingException;
use Webklex\PHPIMAP\Exceptions\MessageNotFoundException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;
use Webklex\PHPIMAP\Message;

class CNCPOController extends Controller
{

    private $message;

    //
    public function __construct(){
        $this->middleware('auth.cncpo');
    }

    /**
     * @throws MaskNotFoundException
     * @throws ConnectionFailedException
     */
    private function download_file() : ?\PhpMimeMailParser\Attachment
    {
        ActionLogController::log(0,"cncpo_system","trying to download cncpo blacklist");

        //Open mail session
        $client = \Webklex\IMAP\Facades\Client::account('cncpo');
        $client->connect();

        //Search for possible cncpo blacklist mails
        try {
            $folder = $client->getFolder('INBOX');
            if (!$folder) {
                ActionLogController::log(0,"cncpo_system","INBOX folder fetching failed");
                return null;
            }

            $message = $folder->messages()->whereFrom(env('CNCPO_FROM_EMAIL'))->get()->first();

        } catch (ConnectionFailedException $e) {
            ActionLogController::log(0,"cncpo_system","connection to mail server failed");
            return null;
        } catch (FolderFetchingException $e) {
            ActionLogController::log(0,"cncpo_system","INBOX folder fetching failed");
            return null;
        } catch (GetMessagesFailedException $e) {
            ActionLogController::log(0,"cncpo_system","messages fetch failed");
            return null;
        } catch (RuntimeException $e) {
            ActionLogController::log(0,"cncpo_system","generic error");
            return null;
        }

        if ($message === null) {
            ActionLogController::log(0,"cncpo_system","there is no cncpo blacklist mail to read, try again later");;;
            return null;
        }

        $msg = new Parser();
        $msg->setText($message->getRawBody());

        $attachments = collect($msg->getAttachments());

        if ($attachments->count() === 0) {
            ActionLogController::log(0,"cncpo_system","found a mail with no attachments");
            return null;
        }

        $attachment = null;
        $attachments->each(function ($att) use (&$attachment) {
            if ($att->getContentType() === 'message/rfc822') {
                $attachment = $att;
            }
        });

        if ($attachment === null) {
            ActionLogController::log(0,"cncpo_system","found a mail with no rfc822 attachments");
            return null;
        }

        $parser = new Parser();
        $parser->setText($attachment->getContent());

        $file = $parser->getAttachments()[0];

        $this->message = $message;

        ActionLogController::log(0,"cncpo_system","succeded to download cncpo blacklist");
        return $file;
    }

    private function send_reply(string $prog, string $id)
    {
        $message = $this->message;

        // Backup your default mailer
        $backup = Mail::getSwiftMailer();
        $transport = new Swift_SmtpTransport(env('CNCPO_PEC_IMAP_HOST'), 465, 'ssl');
        $transport->setUsername(env('CNCPO_PEC_EMAIL'));
        $transport->setPassword(env('CNCPO_PEC_PASSWORD'));
        Mail::setSwiftMailer(new Swift_Mailer($transport));

        $reply = new CNCPOReply($prog, $id);
        $reply
            ->to($message->getFrom()->first())
            ->subject("Re: " . $message->getSubject()->first());

        Mail::send($reply);
        Mail::setSwiftMailer($backup);

    }

    private function decrypt_file(\PhpMimeMailParser\Attachment $attachment)
    {
        ActionLogController::log(0,"cncpo_system","decrypting file");

        $password = env('CNCPO_GPG_PRIVATE_KEY_PASSWORD');

        $gpgPath = preg_replace("/\r\n|\r|\n/",'', shell_exec('which gpg'));
        if (preg_match('/not found/', $gpgPath)) {
            ActionLogController::log(0,"system","GPG is not installed.");
            return null;
        }
        $file = storage_path('app/tmp/' . $attachment->getFilename());
        $decrypted_file = storage_path('app/tmp/blacklist.csv');
        unlink($file);
        unlink($decrypted_file);

        file_put_contents($file, $attachment->getContent());

        exec("$gpgPath --batch --pinentry-mode loopback --passphrase \"$password\" --decrypt --output \"$decrypted_file\" \"$file\" 2>&1", $retArr, $retVal);

        if (!is_file($decrypted_file)) {
            ActionLogController::log(0,"system","Error while decrypting file");
            return null;
        }

        $tmp = fopen($decrypted_file, 'rb+');
        $first_row = fgetcsv($tmp, 5000, "\n");
        $first_row_arr = explode(" ;",$first_row[0]);

        if((count($first_row_arr) == 6) && is_numeric($first_row_arr[0])) {
            $blacklist_id = trim($first_row_arr[0]);
            if(strlen(trim($first_row_arr[1])) == 12){
                $blacklist_timestamp = self::make_datetime(trim($first_row_arr[1]));
                ActionLogController::log(0,"cncpo_system","cncpo blacklist is valid");
                return [
                    "blacklist_id" => $blacklist_id,
                    "balcklist_timestamp" => $blacklist_timestamp,
                    "content" => file_get_contents($decrypted_file)
                ];
            }
        }
        ActionLogController::log(0,"cncpo_system","cncpo blacklist is not valid");
        fclose($tmp);
        return null;
    }

    private function save_file($validation){
        ActionLogController::log(0,"cncpo_system","saving cncpo blacklist");
        $new = new \App\CNCPO\Files();
        $new->blacklist_id = $validation["blacklist_id"];
        $new->blacklist_timestamp = $validation["balcklist_timestamp"];
        $new->content = $validation["content"];
        $new->md5 = md5($validation["content"]);
        if($new->save()){
            ActionLogController::log(0,"cncpo_system","cncpo blacklist saved");
            return $validation["content"];
        }

        ActionLogController::log(0,"cncpo_system","cncpo blacklist not saved");
        return false;
    }

    private function parse_file($save){
        ActionLogController::log(0,"cncpo_system","started blacklist elements update");
        \DB::connection('mysql')->table('cncpo_blacklist')->truncate();
        $tmp = fopen('php://temp', 'r+');
        fwrite($tmp,$save);
        rewind($tmp);
        $count = $total = $success = 0;
        while($row = fgetcsv($tmp, 5000, "\n")){
            $row_arr = explode(" ;",$row[0]);
            $url = substr($row_arr[0],0, 254);
            $fqdn = $row_arr[1];
            if($count > 0){
                $total++;
                $new = new \App\CNCPO\Blacklist();
                $new->url = $url;
                $new->fqdn = $fqdn;
                if($new->save()){
                    $success++;
                }
            }
            $count++;
        }
        ActionLogController::log(0,"cncpo_system","inserted $success of $total blacklist elements");
    }

    public function update_blacklist(){
        if(env("CNCPO_ENABLED") == "1"){
            $check_env = self::check_env();
            if(count($check_env) == 0){
                ActionLogController::log(0,"cncpo_cron","starting run");
                $file = $this->download_file();
                if($file){
                    ActionLogController::log(0,"cncpo_cron","file downloaded");
                    $decripted = $this->decrypt_file($file);
                    if($decripted){
                        ActionLogController::log(0,"cncpo_cron","downloaded file is valid");
                        $save = $this->save_file($decripted);
                        if($save){
                            ActionLogController::log(0,"cncpo_cron","file saved");
                            ActionLogController::log(0,"cncpo_cron","start parsing");
                            $this->parse_file($save);
                            ActionLogController::log(0,"cncpo_cron","parsing ended");
                        }else{
                            ActionLogController::log(0,"cncpo_cron","file save failed",true);
                        }
                        if (env('CNCPO_SEND_REPLY') == "1") {
                            $this->send_reply($decripted['blacklist_id'], $decripted['balcklist_timestamp']);
                        }
                        $this->message->setFlag('Seen');
                        $this->message->move($this->message->getClient()->getFolderByName(env('CNCPO_PEC_IMAP_ARCHIVE_FOLDER'))->path);
                    }else{
                        ActionLogController::log(0,"cncpo_cron","downloaded file is invalid",true);
                    }
                }else{
                    ActionLogController::log(0,"cncpo_cron","file download failed",true);
                }

                ActionLogController::log(0,"cncpo_cron","run ended");
            }else{
                ActionLogController::log(0,"cncpo_cron","run not started because of: ".implode(", ",$check_env),true);
            }
        }
    }

    public function test(){
        $obj = new \StdClass();
        //env
        $env_test = self::check_env();
        $obj->settings = new \StdClass();
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ["Settings formally correct"] : $env_test;
        //download
        if($obj->settings->passed){
            $obj->download = new \StdClass();
            $file = $this->download_file();
            if($file instanceof \PhpMimeMailParser\Attachment){
                $obj->download->passed = true;
                $obj->download->messages = ["File download success"];
            }else{
                $obj->download->passed = false;
                $obj->download->messages = ["File download failed (view action log for more infos)"];
            }
            //validation
            if($obj->download->passed){
                $obj->validation = new \StdClass();
                if($this->decrypt_file($file)){
                    $obj->validation->passed = true;
                    $obj->validation->messages = ["File validation success"];
                }else{
                    $obj->validation->passed = false;
                    $obj->validation->messages = ["File validation failed (view action log for more infos)"];
                }
            }

        }
        return json_encode($obj);
    }

    private static function make_datetime($string){
        $year = (int) substr($string, 0, 4);
        $month = (int) substr($string, 4, 2);
        $day = (int) substr($string, 6, 2);
        $hour = (int) substr($string, 8, 2);
        $minute = (int) substr($string, 10, 2);
        $datetime = new \DateTime();
        $datetime->setDate($year,$month,$day);
        $datetime->setTime($hour,$minute,0);
        return $datetime;
    }

    public function datatable_files(Request $request){
        if($request->ajax()){
            $data = \App\CNCPO\Files::query();
            return Datatables::of($data)
                ->rawColumns(
                    ['blacklist_id',
                    'md5',
                    'timestamp',
                    'blacklist_timestamp']
                )->make(true);
        }
    }

    public function datatable_blacklist(Request $request){
        if($request->ajax()){
            $data = \App\CNCPO\Blacklist::query();
            return Datatables::of($data)
                ->rawColumns(
                    ['url',
                    'fqdn']
                )->make(true);
        }
    }

    public function download_blacklist(Request $request,$type){
        switch ($type) {
            case 'url':
                //by url
                $message = "CNCPO URL blacklist downloaded";
                $list = \App\CNCPO\Blacklist::select('url')->distinct()->pluck('url')->toArray();
            break;
            default:
                //by fqdn
                $message = "CNCPO FQDN blacklist downloaded";
                $list = \App\CNCPO\Blacklist::select('fqdn')->distinct()->pluck('fqdn')->toArray();
            break;
        }
        $content = implode("\n",$list);
        ActionLogController::log(\Auth::user()->id,\Auth::user()->name,$message);
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', "blacklist.txt")
        ];
        return \Response::make($content, 200, $headers);
    }

    private static function check_env(){
        $errors = [];
        if(!env('CNCPO_PEC_EMAIL')){
            $errors[] = "PEC email not filled";
        }
        if(!env('CNCPO_PEC_PASSWORD')){
            $errors[] = "PEC password not filled";;
        }
        if(!env('CNCPO_PEC_IMAP_HOST')){
            $errors[] = "IMAP host not filled";
        }
        if(!env('CNCPO_GPG_PRIVATE_KEY')){
            $errors[] = "GPG private key not filled";
        }
        if(!env('CNCPO_DNS_REDIRECT_IP')){
            $errors[] = "DNS redirect IP not filled";
        }else{
            if(!filter_var(env('CNCPO_DNS_REDIRECT_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "DNS redirect IP not valid";
            }
        }
        return $errors;
    }
}
