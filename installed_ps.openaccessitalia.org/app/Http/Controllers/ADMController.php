<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DataTables;

class ADMController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth.adm');
    }

    private static function get_href($html){
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $a = $doc->getElementsByTagName('a');
        if($a->length == 1){
            return $a[0]->getAttribute("href");
        }
        return false;
    }

    private function find_betting_files_url(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","trying to find betting adm blacklist links");
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get(env("ADM_BETTING_URL"));
        }catch(\GuzzleHttp\Exception\BadResponseException $e){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find betting adm blacklist links (".$e->getMessage().")");
            return false;
        }
        if($response->getStatusCode() == 200){
            if ($response->getBody()) {
                $result = $response->getBody()->getContents();
                $rows = explode("\n",$result);
                $txt = $sha256 = false;
                foreach ($rows as $row) {
                    if(strpos($row, "Elenco dei siti soggetti ad inibizione - txt") !== false){
                        $txt = self::get_href(trim($row));
                    }
                    if(strpos($row, "File di controllo - txt") !== false){
                        $sha256 = self::get_href(trim($row));
                    }
                }
                if($txt && $sha256){
                    $txt = "https://www.adm.gov.it".$txt;
                    $sha256 = "https://www.adm.gov.it".$sha256;
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","betting adm blacklist links founded ($txt | $sha256)");
                    return [
                        "txt" => $txt,
                        "sha256" => $sha256
                    ];
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find betting adm blacklist links (no links found)");
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find betting adm blacklist links (no body)");
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find betting adm blacklist links (".$response->getStatusCode().")");
        }
        return false;
    }

    private function download_betting_files($links){
        $files = [
            "txt" => false,
            "sha256" => false
        ];
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","trying to download betting adm blacklist");
        $ch = \curl_init();

        curl_setopt($ch, CURLOPT_URL, $links["txt"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36');
        
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to download betting adm blacklist (curl error: ".curl_error($ch).")");
            curl_close($ch);
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","succeded to download betting adm blacklist");
            curl_close($ch);
            $files["txt"] = $result;
        }

        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","trying to download betting adm blacklist sha256");
        $ch = \curl_init();

        curl_setopt($ch, CURLOPT_URL, $links["sha256"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36');
        
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to download betting adm blacklist sha256 (curl error: ".curl_error($ch).")");
            curl_close($ch);
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","succeded to download betting adm blacklist sha256");
            curl_close($ch);
            $files["sha256"] = $result;
        }
        if($files["txt"] && $files["sha256"]){
            return $files;
        }
        return false;
    }

    private function validate_betting_file($files){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","validating betting adm blacklist");
        if(hash("sha256",$files["txt"]) == $files["sha256"]){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","betting adm blacklist is valid");
            return $files;
        }
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","betting adm blacklist is not valid");
        return false;
    }

    private function save_betting_file($validation){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","saving betting adm blacklist");
        $new = new \App\ADM\BettingFiles();
        $new->content = $validation["txt"];
        $new->sha256 = $validation["sha256"];
        if($new->save()){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","betting adm blacklist saved");
            return $validation["txt"];
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","betting adm blacklist not saved");
            return false;
        }
    }

    private function parse_betting_file($save){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","started betting adm blacklist elements update");
        \DB::connection('mysql')->table('adm_betting_blacklist')->truncate();
        $rows = explode("\n",$save);
        $total = $success = 0;
        foreach ($rows as $row) {
            $total++;
            $new = new \App\ADM\BettingBlacklist();
            $new->fqdn = trim($row);
            if($new->save()){
                $success++;
            }
        }
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","inserted $success of $total betting adm blacklist elements");
    }

    private function find_smoking_files_url(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","trying to find smoking adm blacklist links");
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get(env("ADM_SMOKING_URL"));
        }catch(\GuzzleHttp\Exception\BadResponseException $e){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find smoking adm blacklist links (".$e->getMessage().")");
            return false;
        }
        if($response->getStatusCode() == 200){
            if ($response->getBody()) {
                $result = $response->getBody()->getContents();
                $rows = explode("\n",$result);
                $txt = $sha256 = false;
                foreach ($rows as $row) {
                    if(strpos($row, "Elenco dei siti soggetti ad inibizione - txt") !== false){
                        $txt = self::get_href(trim($row));
                    }
                    if(strpos($row, "File di controllo - txt") !== false){
                        $sha256 = self::get_href(trim($row));
                    }
                }
                if($txt && $sha256){
                    $txt = "https://www.adm.gov.it".$txt;
                    $sha256 = "https://www.adm.gov.it".$sha256;
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","smoking adm blacklist links founded ($txt | $sha256)");
                    return [
                        "txt" => $txt,
                        "sha256" => $sha256
                    ];
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find smoking adm blacklist links (no links found)");
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find smoking adm blacklist links (no body)");
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to find smoking adm blacklist links (".$response->getStatusCode().")");
        }
        return false;
    }

    private function download_smoking_files($links){
        $files = [
            "txt" => false,
            "sha256" => false
        ];
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","trying to download smoking adm blacklist");
        $ch = \curl_init();

        curl_setopt($ch, CURLOPT_URL, $links["txt"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36');
        
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to download smoking adm blacklist (curl error: ".curl_error($ch).")");
            curl_close($ch);
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","succeded to download smoking adm blacklist");
            curl_close($ch);
            $files["txt"] = $result;
        }

        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","trying to download smoking adm blacklist sha256");
        $ch = \curl_init();

        curl_setopt($ch, CURLOPT_URL, $links["sha256"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36');
        
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","failed to download smoking adm blacklist sha256 (curl error: ".curl_error($ch).")");
            curl_close($ch);
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","succeded to download smoking adm blacklist sha256");
            curl_close($ch);
            $files["sha256"] = $result;
        }
        if($files["txt"] && $files["sha256"]){
            return $files;
        }
        return false;
    }

    private function validate_smoking_file($files){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","validating smoking adm blacklist");
        if(hash("sha256",$files["txt"]) == $files["sha256"]){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","smoking adm blacklist is valid");
            return $files;
        }
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","smoking adm blacklist is not valid");
        return false;
    }

    private function save_smoking_file($validation){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","saving smoking adm blacklist");
        $new = new \App\ADM\SmokingFiles();
        $new->content = $validation["txt"];
        $new->sha256 = $validation["sha256"];
        if($new->save()){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","smoking adm blacklist saved");
            return $validation["txt"];
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","smoking adm blacklist not saved");
            return false;
        }
    }

    private function parse_smoking_file($save){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","started smoking adm blacklist elements update");
        \DB::connection('mysql')->table('adm_smoking_blacklist')->truncate();
        $rows = explode("\n",$save);
        $total = $success = 0;
        foreach ($rows as $row) {
            $total++;
            $new = new \App\ADM\SmokingBlacklist();
            $new->fqdn = trim($row);
            if($new->save()){
                $success++;
            }
        }
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","inserted $success of $total smoking adm blacklist elements");
    }

    public function update_blacklists(){
        if(env("ADM_ENABLED") == "1"){
            $check_env = self::check_env();
            if(count($check_env) == 0){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","starting run");
                $betting_links = $this->find_betting_files_url();
                if($betting_links){
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","betting links found");
                    $betting_files = $this->download_betting_files($betting_links);
                    if($betting_files){
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","betting files downloaded");
                        $betting_validation = $this->validate_betting_file($betting_files);
                        if($betting_validation){
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","betting file is valid");
                            $betting_save = $this->save_betting_file($betting_validation);
                            if($betting_save){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","betting file saved");
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","start betting parsing");
                                $this->parse_betting_file($betting_save);
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","parsing betting ended");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","betting file is invalid",true);
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","betting files download failed",true);
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","betting links not found",true);
                }
                $smoking_links = $this->find_smoking_files_url();
                if($smoking_links){
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","smoking links found");
                    $smoking_files = $this->download_smoking_files($smoking_links);
                    if($smoking_files){
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","smoking files downloaded");
                        $smoking_validation = $this->validate_smoking_file($smoking_files);
                        if($smoking_validation){
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","smoking file is valid");
                            $smoking_save = $this->save_smoking_file($smoking_validation);
                            if($smoking_save){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","smoking file saved");
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","start smoking parsing");
                                $this->parse_smoking_file($smoking_save);
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","parsing smoking ended");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","smoking file is invalid",true);
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","smoking files download failed",true);
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","smoking links not found",true);
                }
                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","run ended");
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_cron","run not started because of: ".implode(", ",$check_env),true);
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
        if($obj->settings->passed){
            //betting links
            $obj->betting_links = new \StdClass();
            $betting_links = $this->find_betting_files_url();
            if($betting_links){
                $obj->betting_links->passed = true;
                $obj->betting_links->messages = ["Betting links founded","txt: ".$betting_links["txt"],"sha256: ".$betting_links["sha256"]];
            }else{
                $obj->betting_links->passed = false;
                $obj->betting_links->messages = ["Betting links not founded"];
            }
            if($obj->betting_links->passed){
                //betting download
                $obj->betting_download = new \StdClass();
                $betting_files = $this->download_betting_files($betting_links);
                if($betting_files){
                    $obj->betting_download->passed = true;
                    $obj->betting_download->messages = ["Files download success"];
                }else{
                    $obj->betting_download->passed = false;
                    $obj->betting_download->messages = ["Files download failed (view action log for more infos)"];
                }
                if($obj->betting_download->passed){
                    //betting validation
                    $obj->betting_validation = new \StdClass();
                    $betting_validation = $this->validate_betting_file($betting_files);
                    if($betting_validation){
                        $obj->betting_validation->passed = true;
                        $obj->betting_validation->messages = ["File validation success"];
                    }else{
                        $obj->betting_validation->passed = false;
                        $obj->betting_validation->messages = ["File validation failed (view action log for more infos)"];
                    }
                }
            }
            //smoking links
            $obj->smoking_links = new \StdClass();
            $smoking_links = $this->find_smoking_files_url();
            if($smoking_links){
                $obj->smoking_links->passed = true;
                $obj->smoking_links->messages = ["smoking links founded","txt: ".$smoking_links["txt"],"sha256: ".$smoking_links["sha256"]];
            }else{
                $obj->smoking_links->passed = false;
                $obj->smoking_links->messages = ["smoking links not founded"];
            }
            if($obj->smoking_links->passed){
                //smoking download
                $obj->smoking_download = new \StdClass();
                $smoking_files = $this->download_smoking_files($smoking_links);
                if($smoking_files){
                    $obj->smoking_download->passed = true;
                    $obj->smoking_download->messages = ["Files download success"];
                }else{
                    $obj->smoking_download->passed = false;
                    $obj->smoking_download->messages = ["Files download failed (view action log for more infos)"];
                }
                if($obj->smoking_download->passed){
                    //smoking validation
                    $obj->smoking_validation = new \StdClass();
                    $smoking_validation = $this->validate_smoking_file($smoking_files);
                    if($smoking_validation){
                        $obj->smoking_validation->passed = true;
                        $obj->smoking_validation->messages = ["File validation success"];
                    }else{
                        $obj->smoking_validation->passed = false;
                        $obj->smoking_validation->messages = ["File validation failed (view action log for more infos)"];
                    }
                }
            }
        }
        return json_encode($obj);
    }

    public function download_betting_blacklist(Request $request){
        $list = \App\ADM\BettingBlacklist::select('fqdn')->distinct()->pluck('fqdn')->toArray();
        $content = implode("\n",$list);
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","FQDN betting adm blacklist pulled by ".$request->ip());
        $headers = [
            'Content-type' => 'text/plain', 
            'Content-Disposition' => sprintf('attachment; filename="%s"', "betting_blacklist.txt")
        ];
        return \Response::make($content, 200, $headers);
    }

    public function download_smoking_blacklist(Request $request){
        $list = \App\ADM\SmokingBlacklist::select('fqdn')->distinct()->pluck('fqdn')->toArray();
        $content = implode("\n",$list);
        \App\Http\Controllers\Admin\ActionLogController::log(0,"adm_system","FQDN smoking adm blacklist pulled by ".$request->ip());
        $headers = [
            'Content-type' => 'text/plain', 
            'Content-Disposition' => sprintf('attachment; filename="%s"', "smoking_blacklist.txt")
        ];
        return \Response::make($content, 200, $headers);
    }

    public function datatable_betting_blacklist(Request $request){
        if($request->ajax()){
            $data = \App\ADM\BettingBlacklist::orderBy("fqdn","asc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['fqdn']
                )->make(true);
        }
    }

    public function datatable_betting_files(Request $request){
        if($request->ajax()){
            $data = \App\ADM\BettingFiles::orderBy("timestamp","desc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['timestamp',
                    'sha256']
                )->make(true);
        }
    }

    public function datatable_smoking_blacklist(Request $request){
        if($request->ajax()){
            $data = \App\ADM\SmokingBlacklist::orderBy("fqdn","asc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['fqdn']
                )->make(true);
        }
    }

    public function datatable_smoking_files(Request $request){
        if($request->ajax()){
            $data = \App\ADM\SmokingFiles::orderBy("timestamp","desc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['timestamp',
                    'sha256']
                )->make(true);
        }
    }

    private static function check_env(){
        $errors = [];
        if(!env('ADM_BETTING_URL')){
            $errors[] = "Betting URL not filled";
        }else{
            if(!filter_var(env('ADM_BETTING_URL'), FILTER_VALIDATE_URL)){
                $errors[] = "Betting URL not valid";
            }
        }
        if(!env('ADM_SMOKING_URL')){
            $errors[] = "Smoking URL not filled";
        }else{
            if(!filter_var(env('ADM_SMOKING_URL'), FILTER_VALIDATE_URL)){
                $errors[] = "Smoking URL not valid";
            }
        }
        if(!env('ADM_DNS_REDIRECT_IP')){
            $errors[] = "DNS redirect IP not filled";
        }else{
            if(!filter_var(env('ADM_DNS_REDIRECT_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "DNS redirect IP not valid";
            }
        }
        return $errors;
    }

}
