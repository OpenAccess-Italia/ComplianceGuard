<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DataTables;
use Carbon\Carbon;

class CNCPOController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth.cncpo');
    }

    private function download_file(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","trying to download cncpo blacklist");
        $ch = \curl_init();

        curl_setopt($ch, CURLOPT_URL, env("CNCPO_DOWNLOAD_URL"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, "P12");
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, env("CNCPO_PFX_PASS"));
        curl_setopt($ch, CURLOPT_SSLCERT, env("CNCPO_PFX_PATH"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36');
        
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","failed to download cncpo blacklist (curl error: ".curl_error($ch).")");
            curl_close($ch);
            return false;
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","succeded to download cncpo blacklist");
            curl_close($ch);
            return $result;
        }
        
    }

    private function validate_file($content){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","validating cncpo blacklist");
        $tmp = fopen('php://temp', 'r+');
        fwrite($tmp,$content);
        rewind($tmp);
        $first_row = fgetcsv($tmp, 5000, "\n");
        $first_row_arr = explode(" ;",$first_row[0]);
        if(count($first_row_arr) == 6){
            if(is_numeric($first_row_arr[0])){
                $blacklist_id = trim($first_row_arr[0]);
                if(strlen(trim($first_row_arr[1])) == 12){
                    $blacklist_timestamp = self::make_datetime(trim($first_row_arr[1]));
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","cncpo blacklist is valid");
                    return [
                        "blacklist_id" => $blacklist_id,
                        "balcklist_timestamp" => $blacklist_timestamp,
                        "content" => $content
                    ];
                    fclose($tmp);
                }
            }
        }
        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","cncpo blacklist is not valid");
        fclose($tmp);
        return false;
    }

    private function save_file($validation){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","saving cncpo blacklist");
        $new = new \App\CNCPO\Files();
        $new->blacklist_id = $validation["blacklist_id"];
        $new->blacklist_timestamp = $validation["balcklist_timestamp"];
        $new->content = $validation["content"];
        $new->md5 = md5($validation["content"]);
        if($new->save()){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","cncpo blacklist saved");
            return $validation["content"];
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","cncpo blacklist not saved");
            return false;
        }
    }

    private function parse_file($save){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","started blacklist elements update");
        \DB::connection('mysql')->table('cncpo_blacklist')->truncate();
        $tmp = fopen('php://temp', 'r+');
        fwrite($tmp,$save);
        rewind($tmp);
        $count = $total = $success = 0;
        while($row = fgetcsv($tmp, 5000, "\n")){
            $row_arr = explode(" ;",$row[0]);
            $url = $row_arr[0];
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
        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_system","inserted $success of $total blacklist elements");
    }

    public function update_blacklist(){
        if(env("CNCPO_ENABLED") == "1"){
            $check_env = self::check_env();
            if(count($check_env) == 0){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","starting run");
                $file = $this->download_file();
                if($file){
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","file downloaded");
                    $validation = $this->validate_file($file);
                    if($validation){
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","downloaded file is valid");
                        $save = $this->save_file($validation);
                        if($save){
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","file saved");
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","start parsing");
                            $this->parse_file($save);
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","parsing ended");
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","file save failed",true);
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","downloaded file is invalid",true);
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","file download failed",true);
                }
                \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","run ended");
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"cncpo_cron","run not started because of: ".implode(", ",$check_env),true);
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
            if($file !== false){
                $obj->download->passed = true;
                $obj->download->messages = ["File download success"];
            }else{
                $obj->download->passed = false;
                $obj->download->messages = ["File download failed (view action log for more infos)"];
            }
            //validation
            if($obj->download->passed){
                $obj->validation = new \StdClass();
                $validation = $this->validate_file($file);
                if($validation){
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
        $year = intval(substr($string,0,4));
        $month = intval(substr($string,4,2));
        $day = intval(substr($string,6,2));
        $hour = intval(substr($string,8,2));
        $minute = intval(substr($string,10,2));
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
        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,$message);
        $headers = [
            'Content-type' => 'text/plain', 
            'Content-Disposition' => sprintf('attachment; filename="%s"', "blacklist.txt")
        ];
        return \Response::make($content, 200, $headers);
    }

    private static function check_env(){
        $errors = [];
        if(!env('CNCPO_DOWNLOAD_URL')){
            $errors[] = "Download URL not filled";
        }else{
            if(!filter_var(env('CNCPO_DOWNLOAD_URL'), FILTER_VALIDATE_URL)){
                $errors[] = "Download URL not valid";
            }
        }
        if(!env('CNCPO_PFX_PATH')){
            $errors[] = "PFX path not filled";
        }else{
            if(!file_exists(env('CNCPO_PFX_PATH'))){
                $errors[] = "PFX file do not exists";
            }
        }
        if(!env('CNCPO_PFX_PASS')){
            $errors[] = "PFX password not filled";
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
