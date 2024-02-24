<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;
use Carbon\Carbon;

class AdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    public function datatable_actions_log(Request $request){
        if($request->ajax()){
            if($request->input('hide_system_cron') == "true"){
                $data = \App\ActionLog::where('timestamp', '>=', Carbon::now()->subDay())->where("user_id","<>","0")->orderBy("id","desc")->get();
            }else{
                $data = \App\ActionLog::where('timestamp', '>=', Carbon::now()->subDay())->orderBy("id","desc")->get();
            }
            return Datatables::of($data)->addColumn('action',function($row){
                return htmlspecialchars($row->action);
            })->make(true);
        }
    }

    public function datatable_ps_api_log(Request $request){
        $data = \App\Piracy\APILog::where('timestamp', '>=', Carbon::now()->subDay())->orderBy("id","desc")->get();
        return Datatables::of($data)->make(true);
    }

    public function datatable_ps_access_tokens(Request $request){
        $data = \App\Piracy\APIAccessTokens::orderBy("id","desc")->get();
        return Datatables::of($data)->make(true);
    }

    public function datatable_ps_refresh_tokens(Request $request){
        $data = \App\Piracy\APIRefreshTokens::orderBy("id","desc")->get();
        return Datatables::of($data)->make(true);
    }

    public function save_settings(Request $request){
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            if(self::env_exist($key) && self::env_auth($key)){
                $value = ($value == "null") ? null : $value;
                if(env($key) != $value){
                    self::update_env($key,$value);
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"updated setting $key from ".env($key)." to $value");
                }
            }
        }
        //settings files updates
        self::make_network_settings_file();
        \App\Http\Controllers\Admin\BGPController::make_settings_file();
        if(env("PIRACY_SHIELD_ENABLED") == "1"){
            \App\Http\Controllers\PiracyController::make_piracy_settings_files();
        }
        return redirect('/admin/settings/edit');
    }

    private static function update_env($key,$value){
        $path = base_path('.env');
        if(file_exists($path)){
            switch ($key) {
                case 'MAIL_USERNAME':
                case 'MAIL_PASSWORD':
                case 'MAIL_ENCRYPTION':
                    if(env($key) == null){
                        if($value == "" || $value == "null" || $value == null){
                            file_put_contents($path, str_replace("$key=null","$key=null", file_get_contents($path)));
                        }else{
                            file_put_contents($path, str_replace("$key=null","$key=\"$value\"", file_get_contents($path)));
                        }
                    }else{
                        if($value == "" || $value == "null" || $value == null){
                            file_put_contents($path, str_replace("$key=\"".env($key)."\"","$key=null", file_get_contents($path)));
                        }else{
                            file_put_contents($path, str_replace("$key=\"".env($key)."\"","$key=\"$value\"", file_get_contents($path)));
                        }
                    }
                break;
                case 'PIRACY_SHIELD_VPN_PSK':
                    file_put_contents($path, str_replace("$key=\"".env($key)."\"","$key=\"".base64_encode($value)."\"", file_get_contents($path)));
                    $_ENV[$key] = $value;
                break;
                default:
                    file_put_contents($path, str_replace("$key=\"".env($key)."\"","$key=\"$value\"", file_get_contents($path)));
                    $_ENV[$key] = $value;
                break;
            }
        }
    }

    private static function env_exist($key){
        switch ($key) {
            case 'MAIL_USERNAME':
            case 'MAIL_PASSWORD':
            case 'MAIL_ENCRYPTION':
                return true;
            break;
            default:
                return env($key) !== null;
            break;
        }
    }

    private static function env_auth($key){
        return in_array($key,["PIRACY_SHIELD_VPN_PEER_IP","PIRACY_SHIELD_VPN_REMOTE_LAN_IP","PIRACY_SHIELD_VPN_LOCAL_LAN_IP","PIRACY_SHIELD_VPN_PSK",
            "PIRACY_SHIELD_ENABLED","PIRACY_SHIELD_MAIL","PIRACY_SHIELD_PSW","PIRACY_SHIELD_API_URL","PIRACY_SHIELD_DNS_REDIRECT_IP",
            "NET_IP","NET_MASK","NET_GATEWAY","EXTERNAL_DNS_SERVERS",
            "BGP_ROUTER_IP","BGP_ASN","BGP_LOCAL_IP","BGP_LOCAL_MASK","BGP_LOCAL_GATEWAY",
            "DNS_SERVER_PRIMARY_IP","DNS_SERVER_PRIMARY_PORT","DNS_SERVER_PRIMARY_USER","DNS_SERVER_PRIMARY_PSW","DNS_SERVER_PRIMARY_PRIVKEY","DNS_SERVER_PRIMARY_PATH","DNS_SERVER_PRIMARY_RELOAD","DNS_SERVER_PRIMARY_EXPORT_PLAIN",
            "DNS_SERVER_SECONDARY_IP","DNS_SERVER_SECONDARY_PORT","DNS_SERVER_SECONDARY_USER","DNS_SERVER_SECONDARY_PSW","DNS_SERVER_SECONDARY_PRIVKEY","DNS_SERVER_SECONDARY_PATH","DNS_SERVER_SECONDARY_RELOAD","DNS_SERVER_SECONDARY_EXPORT_PLAIN",
            "CNCPO_ENABLED","CNCPO_DOWNLOAD_URL","CNCPO_PFX_PATH","CNCPO_PFX_PASS","CNCPO_DNS_REDIRECT_IP",
            "ADM_ENABLED","ADM_BETTING_URL","ADM_SMOKING_URL","ADM_DNS_REDIRECT_IP",
            "MANUAL_ENABLED","MANUAL_DNS_REDIRECT_IP",
            "MAIL_HOST","MAIL_PORT","MAIL_USERNAME","MAIL_PASSWORD","MAIL_ENCRYPTION","MAIL_FROM_ADDRESS","MAIL_FROM_NAME","MAIL_TO_ADDRESSES",
            "LOGS_DAYS_ACTION","LOGS_DAYS_AUTHENTICATION","LOGS_DAYS_PS_API","LOGS_DAYS_PS_API_ACCESS_TOKENS","LOGS_DAYS_PS_API_REFRESH_TOKENS"
        ]);
    }

    public function update_dns(){
        $check_env = self::check_env_dns();
        if(count($check_env) == 0){
            $dns1 = new \App\Http\Controllers\Admin\DNSController(env('DNS_SERVER_PRIMARY_IP'),env('DNS_SERVER_PRIMARY_PORT'),
                                                                  env('DNS_SERVER_PRIMARY_USER'),env('DNS_SERVER_PRIMARY_PSW'),env('DNS_SERVER_PRIMARY_PRIVKEY'),
                                                                  env('DNS_SERVER_PRIMARY_PATH'),env('DNS_SERVER_PRIMARY_RELOAD'),env('DNS_SERVER_PRIMARY_EXPORT_PLAIN'));
            $dns1->update();
            if(env('DNS_SERVER_SECONDARY_IP')){
                $dns2 = new \App\Http\Controllers\Admin\DNSController(env('DNS_SERVER_SECONDARY_IP'),env('DNS_SERVER_SECONDARY_PORT'),
                                                                      env('DNS_SERVER_SECONDARY_USER'),env('DNS_SERVER_SECONDARY_PSW'),env('DNS_SERVER_SECONDARY_PRIVKEY'),
                                                                      env('DNS_SERVER_SECONDARY_PATH'),env('DNS_SERVER_SECONDARY_RELOAD'),env('DNS_SERVER_SECONDARY_EXPORT_PLAIN'));
                $dns2->update();
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","secondary DNS server IP not set, skipping run");
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","run not started because of: ".implode(", ",$check_env));
        }
    }

    public function update_bgp(){
        $check_env = \App\Http\Controllers\Admin\BGPController::check_env();
        if(count($check_env) == 0){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_cron","starting run");
            $c = new \App\Http\Controllers\Admin\BGPController();
            $c->make_ipv4_list_file();
            $c->make_ipv6_list_file();
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_cron","run ended");
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_cron","run not started because of: ".implode(", ",$check_env));
        }
    }

    public function log_retention(){
        $check_env = \App\Http\Controllers\Admin\ActionLogController::check_env();
        if(count($check_env) == 0){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_retention_cron","starting run");
            $c = new \App\Http\Controllers\Admin\ActionLogController();
            $c->log_retention();
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_retention_cron","run ended");
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_retention_cron","run not started because of: ".implode(", ",$check_env));
        }
    }

    private static function check_env_dns(){
        $errors = [];
        if(!env('DNS_SERVER_PRIMARY_IP')){
            $errors[] = "Primary DNS server IP not filled";
        }else{
            if(!filter_var(env('DNS_SERVER_PRIMARY_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "Primary DNS server IP not valid";
            }else{
                if(!env('DNS_SERVER_PRIMARY_PORT')){
                    $errors[] = "Primary DNS server SSH port not filled";
                }else{
                    if(!is_numeric(env('DNS_SERVER_PRIMARY_PORT'))){
                        $errors[] = "Primary DNS server SSH port not valid";
                    }
                }
                if(!env('DNS_SERVER_PRIMARY_USER')){
                    $errors[] = "Primary DNS server SSH username not filled";
                }
                if(!env('DNS_SERVER_PRIMARY_PSW') && !env('DNS_SERVER_PRIMARY_PRIVKEY')){
                    $errors[] = "Either primary DNS server SSH password or private key must be filled";
                }
                if(!env('DNS_SERVER_PRIMARY_PATH')){
                    $errors[] = "Primary DNS server zone path not filled";
                }
                if(!env('DNS_SERVER_PRIMARY_RELOAD')){
                    $errors[] = "Primary DNS server reload command not filled";
                }
                if(!env('DNS_SERVER_PRIMARY_EXPORT_PLAIN')){
                    $errors[] = "Primary DNS server export plain flag not filled";
                }
                if(env('DNS_SERVER_SECONDARY_IP')){
                    if(!filter_var(env('DNS_SERVER_SECONDARY_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                        $errors[] = "Secondary DNS server IP not valid";
                    }
                    if(!env('DNS_SERVER_SECONDARY_PORT')){
                        $errors[] = "Primary DNS server SSH port not filled";
                    }else{
                        if(!is_numeric(env('DNS_SERVER_SECONDARY_PORT'))){
                            $errors[] = "Primary DNS server SSH port not valid";
                        }
                    }
                    if(!env('DNS_SERVER_SECONDARY_USER')){
                        $errors[] = "Secondary DNS server SSH username not filled";
                    }
                    if(!env('DNS_SERVER_SECONDARY_PSW') && !env('DNS_SERVER_SECONDARY_PRIVKEY')){
                        $errors[] = "Either secondary DNS server SSH password or private key must be filled";
                    }
                    if(!env('DNS_SERVER_SECONDARY_PATH')){
                        $errors[] = "Secondary DNS server zone path not filled";
                    }
                    if(!env('DNS_SERVER_SECONDARY_RELOAD')){
                        $errors[] = "Secondary DNS server reload command not filled";
                    }
                    if(!env('DNS_SERVER_SECONDARY_EXPORT_PLAIN')){
                        $errors[] = "Secondary DNS server export plain flag not filled";
                    }
                }
            }
        }
        return $errors;
    }

    private static function check_env_smtp(){
        $errors = [];
        if(!filter_var(env('MAIL_HOST'), FILTER_VALIDATE_URL)){
            if(!self::validateFQDN(env('MAIL_HOST'))){
                $errors[] = "'STMP server host' is neither a valid host nor a valid IP";
            }
        }
        if(!filter_var(env('MAIL_FROM_ADDRESS'),FILTER_VALIDATE_EMAIL)){
            $errors[] = "'From address' is not a valid mail address";
        }
        if(env('MAIL_FROM_NAME') == "" || !env('MAIL_FROM_NAME')){
            $errors[] = "'From name' not filled";
        }
        if(env('MAIL_TO_ADDRESSES') == "" || !env('MAIL_TO_ADDRESSES')){
            $errors[] = "'To addresses' not filled";
        }else{
            $invalid_addresses = [];
            $raw_to_send = explode(",",env('MAIL_TO_ADDRESSES'));
            foreach ($raw_to_send as $address) {
                if(!filter_var($address,FILTER_VALIDATE_EMAIL)){
                    $invalid_addresses[] = $address;
                }
            }
            if(count($invalid_addresses) > 0){
                $errors[] = "'To addresses' contains some invalid addresses: ".implode(", ",$invalid_addresses);
            }
        }
        return $errors;
    }

    public static function check_env_network(){
        $errors = [];
        if(!env('NET_IP')){
            $errors[] = "Network IP not filled";
        }else{
            if(!filter_var(env('NET_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "Network IP not valid";
            }
        }
        if(!env('NET_MASK')){
            $errors[] = "Network netmask not filled";
        }else{
            if(!filter_var(env('NET_MASK'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "Network netmask not valid";
            }
        }
        if(!env('NET_GATEWAY')){
            $errors[] = "Network gateway not filled";
        }else{
            if(!filter_var(env('NET_GATEWAY'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "Network gateway not valid";
            }
        }
        return $errors;
    }

    public function test_dns(){
        $obj = new \StdClass();
        //env
        $env_test = self::check_env_dns();
        $obj->settings = new \StdClass();
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ["Settings formally correct"] : $env_test;
        if($obj->settings->passed){
            $dns1 = new \App\Http\Controllers\Admin\DNSController(env('DNS_SERVER_PRIMARY_IP'),env('DNS_SERVER_PRIMARY_PORT'),
                                                                  env('DNS_SERVER_PRIMARY_USER'),env('DNS_SERVER_PRIMARY_PSW'),env('DNS_SERVER_PRIMARY_PRIVKEY'),
                                                                  env('DNS_SERVER_PRIMARY_PATH'),env('DNS_SERVER_PRIMARY_RELOAD'),env('DNS_SERVER_PRIMARY_EXPORT_PLAIN'));
            $obj->primary = $dns1->test();
            if(env('DNS_SERVER_SECONDARY_IP')){
                $dns2 = new \App\Http\Controllers\Admin\DNSController(env('DNS_SERVER_SECONDARY_IP'),env('DNS_SERVER_SECONDARY_PORT'),
                                                                      env('DNS_SERVER_SECONDARY_USER'),env('DNS_SERVER_SECONDARY_PSW'),env('DNS_SERVER_SECONDARY_PRIVKEY'),
                                                                      env('DNS_SERVER_SECONDARY_PATH'),env('DNS_SERVER_SECONDARY_RELOAD'),env('DNS_SERVER_SECONDARY_EXPORT_PLAIN'));
                $obj->secondary = $dns2->test();
            }
        }
        return json_encode($obj);
    }

    public function test_bgp(){
        $obj = new \StdClass();
        //env
        $env_test = \App\Http\Controllers\Admin\BGPController::check_env();
        $obj->settings = new \StdClass();
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ["Settings formally correct"] : $env_test;
        return json_encode($obj);
    }

    public function test_smtp(){
        $obj = new \StdClass();
        //env
        $env_test = self::check_env_smtp();
        $obj->settings = new \StdClass();
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ["Settings formally correct"] : $env_test;
        if($obj->settings->passed){
            $obj->testmail = new \StdClass();
            $to_send = \App\Http\Controllers\Admin\ActionLogController::notify_to_send();
            try {
                \Mail::send('mail.notify_error',['system' => "testmail",'error' => "testmail"],
                    function (\Illuminate\Mail\Message $message) use ($to_send){
	            	    $message->subject(env('APP_NAME').": test mail");
	            	    $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
                        $message->to($to_send);
                    }
                );
                $obj->testmail->passed = true;
                $obj->testmail->messages = ["Mail sent to ".implode(", ",$to_send)." - please check your inbox mail"];
            } catch (\Exception $e) {
                $obj->testmail->passed = false;
                $obj->testmail->messages = ["Mail send to ".implode(", ",$to_send)." failed - ".$e->getMessage()];
            }
        }
        return json_encode($obj);
    }

    private static function validateFQDN($domain) {
        $pattern = "/(?=^.{1,254}$)(^(?:(?!\d+\.|-)[a-zA-Z0-9_\-]{1,63}(?<!-)\.?)+(?:[a-zA-Z]{2,})$)/";
        if (preg_match($pattern, $domain)) {
            return true;
        } else {
            return false;
        }
    }
    
    private static function make_network_settings_file(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"system","trying to make network settings file in '".base_path('storage/settings/').'network.csv'."'");
        $check_env = self::check_env_network();
        if(count($check_env) == 0){
            $content = '';
            $content .= 'IP,'.env('NET_IP')."\n";
            $content .= 'MASK,'.env('NET_MASK')."\n";
            $content .= 'GW,'.env('NET_GATEWAY')."\n";
            try{
                file_put_contents(base_path('storage/settings/').'network.csv',$content);
                \App\Http\Controllers\Admin\ActionLogController::log(0,"system","succeded to make network settings file in '".base_path('storage/settings/').'network.csv'."'");
                return true;
            }catch(\Exception $e){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"system","failed to make network settings file in '".base_path('storage/settings/').'network.csv'."' (".$e->getMessage().")",true);
                return false;
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"system","network settings file not made because of: ".implode(", ",$check_env));
        }
    }

}
