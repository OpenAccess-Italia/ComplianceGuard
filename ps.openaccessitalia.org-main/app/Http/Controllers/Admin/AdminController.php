<?php

namespace App\Http\Controllers\Admin;

use App\Events\GpgKeyUpdated;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PiracyController;
use App\Models\ActionLog;
use App\Models\Piracy\APIAccessTokens;
use App\Models\Piracy\APILog;
use App\Models\Piracy\APIRefreshTokens;
use App\SettingKeys;
use Auth;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Mail;
use Settings;

class AdminController extends Controller
{
    public function datatable_actions_log(Request $request){
        if($request->ajax()){
            if($request->input('hide_system_cron') == "true"){
                $data = ActionLog::where("user_id","<>","0");
            }else{
                $data = ActionLog::query();
            }
            return Datatables::of($data)->addColumn('action',function($row){
                return htmlspecialchars($row->action, ENT_QUOTES | ENT_HTML5);
            })->make(true);
        }
    }

    public function datatable_ps_api_log(Request $request){
        $data = APILog::query();
        return Datatables::of($data)->make(true);
    }

    public function datatable_ps_access_tokens(Request $request){
        $data = APIAccessTokens::query();
        return Datatables::of($data)->make(true);
    }

    public function datatable_ps_refresh_tokens(Request $request){
        $data = APIRefreshTokens::query();
        return Datatables::of($data)->make(true);
    }

    public function save_settings(Request $request)
    {
        $data = $request->except('_token');
        $updated = [];

        $valid = collect(SettingKeys::cases())->pluck('name')->toArray();

        foreach ($data as $key => $value) {
            if (in_array($key, $valid, true) && $value != settings(SettingKeys::{$key})) {
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "updated setting $key from ".settings(SettingKeys::{$key})." to $value");
                Settings::set(SettingKeys::{$key}, $value);
                $updated[] = $key;
            }
        }

        if (
            (in_array('CNCPO_GPG_PRIVATE_KEY', $updated) || in_array('CNCPO_GPG_PRIVATE_KEY_PASSWORD', $updated))
            && Settings::get(SettingKeys::CNCPO_ENABLED) == '1')
        {
            // Trigger event to import GPG key
            GpgKeyUpdated::dispatch();
        }

        // settings files updates
        self::make_network_settings_file();
        BGPController::make_settings_file();
        if (Settings::get(SettingKeys::PIRACY_SHIELD_ENABLED)) {
            PiracyController::make_piracy_settings_files();
        }

        return redirect('/admin/settings/edit');
    }


    public function update_dns()
    {
        $check_env = self::check_env_dns();
        if (count($check_env) == 0) {
            $dns1 = new DNSController(Settings::get(SettingKeys::DNS_SERVER_PRIMARY_IP), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PORT), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_USER), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PSW), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PRIVKEY), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PATH), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_RELOAD), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_EXPORT_PLAIN));
            $dns1->update();
            if (Settings::get(SettingKeys::DNS_SERVER_SECONDARY_IP)) {
                $dns2 = new DNSController(Settings::get(SettingKeys::DNS_SERVER_SECONDARY_IP), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PORT), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_USER), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PSW), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PRIVKEY), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PATH), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_RELOAD), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_EXPORT_PLAIN));
                $dns2->update();
            } else {
                ActionLogController::log(0, 'dns_cron', 'secondary DNS server IP not set, skipping run');
            }
        } else {
            ActionLogController::log(0, 'dns_cron', 'run not started because of: '.implode(', ', $check_env));
        }
    }

    public function update_bgp()
    {
        $check_env = BGPController::check_env();
        if (count($check_env) == 0) {
            ActionLogController::log(0, 'bgp_cron', 'starting run');
            $c = new BGPController;
            $c->make_ipv4_list_file();
            $c->make_ipv6_list_file();
            ActionLogController::log(0, 'bgp_cron', 'run ended');
        } else {
            ActionLogController::log(0, 'bgp_cron', 'run not started because of: '.implode(', ', $check_env));
        }
    }

    private static function check_env_dns()
    {
        $errors = [];
        if (! Settings::get(SettingKeys::DNS_SERVER_PRIMARY_IP)) {
            $errors[] = 'Primary DNS server IP not filled';
        } elseif (! filter_var(Settings::get(SettingKeys::DNS_SERVER_PRIMARY_IP), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'Primary DNS server IP not valid';
        } else {
            if (! Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PORT)) {
                $errors[] = 'Primary DNS server SSH port not filled';
            } else {
                if (! is_numeric(Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PORT))) {
                    $errors[] = 'Primary DNS server SSH port not valid';
                }
            }
            if (! Settings::get(SettingKeys::DNS_SERVER_PRIMARY_USER)) {
                $errors[] = 'Primary DNS server SSH username not filled';
            }
            if (! Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PSW) && !Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PRIVKEY)) {
                $errors[] = 'Either primary DNS server SSH password or private key must be filled';
            }
            if (! Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PATH)) {
                $errors[] = 'Primary DNS server zone path not filled';
            }
            if (! Settings::get(SettingKeys::DNS_SERVER_PRIMARY_RELOAD)) {
                $errors[] = 'Primary DNS server reload command not filled';
            }
            if(! Settings::get(SettingKeys::DNS_SERVER_PRIMARY_EXPORT_PLAIN)){
                $errors[] = "Primary DNS server export plain flag not filled";
            }
            if (Settings::get(SettingKeys::DNS_SERVER_SECONDARY_IP)) {
                if (! filter_var(Settings::get(SettingKeys::DNS_SERVER_SECONDARY_IP), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $errors[] = 'Secondary DNS server IP not valid';
                }
                if (! Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PORT)) {
                    $errors[] = 'Primary DNS server SSH port not filled';
                } elseif (! is_numeric(Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PORT))) {
                    $errors[] = 'Primary DNS server SSH port not valid';
                }
                if (! Settings::get(SettingKeys::DNS_SERVER_SECONDARY_USER)) {
                    $errors[] = 'Secondary DNS server SSH username not filled';
                }
                if (! Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PSW) && !Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PRIVKEY)) {
                    $errors[] = 'Either secondary DNS server SSH password or private key must be filled';
                }
                if (! Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PATH)) {
                    $errors[] = 'Secondary DNS server zone path not filled';
                }
                if (! Settings::get(SettingKeys::DNS_SERVER_SECONDARY_RELOAD)) {
                    $errors[] = 'Secondary DNS server reload command not filled';
                }
                if(! Settings::get(SettingKeys::DNS_SERVER_SECONDARY_EXPORT_PLAIN)){
                    $errors[] = "Secondary DNS server export plain flag not filled";
                }

            }
        }

        return $errors;
    }

    private static function check_env_smtp()
    {
        $errors = [];
        if (!filter_var(Settings::get(SettingKeys::MAIL_HOST),FILTER_VALIDATE_URL)
            && !self::validateFQDN(Settings::get(SettingKeys::MAIL_HOST)))
        {
            $errors[] = "'STMP server host' is neither a valid host nor a valid IP";
        }
        if (! filter_var(Settings::get(SettingKeys::MAIL_FROM_ADDRESS), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "'From address' is not a valid mail address";
        }
        if (Settings::get(SettingKeys::MAIL_FROM_NAME) == '' || ! Settings::get(SettingKeys::MAIL_FROM_NAME)) {
            $errors[] = "'From name' not filled";
        }
        if (Settings::get(SettingKeys::MAIL_TO_ADDRESSES) == '' || ! Settings::get(SettingKeys::MAIL_TO_ADDRESSES)) {
            $errors[] = "'To addresses' not filled";
        } else {
            $invalid_addresses = [];
            foreach (explode(',', Settings::get(SettingKeys::MAIL_TO_ADDRESSES)) as $address) {
                if (! filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    $invalid_addresses[] = $address;
                }
            }
            if (count($invalid_addresses) > 0) {
                $errors[] = "'To addresses' contains some invalid addresses: ".implode(', ', $invalid_addresses);
            }
        }

        return $errors;
    }

    public static function check_env_network()
    {
        $errors = [];
        if (! Settings::get(SettingKeys::NET_IP)) {
            $errors[] = 'Network IP not filled';
        } elseif (! filter_var(Settings::get(SettingKeys::NET_IP), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'Network IP not valid';
        }
        if (! Settings::get(SettingKeys::NET_MASK)) {
            $errors[] = 'Network netmask not filled';
        } else {
            if (! filter_var(Settings::get(SettingKeys::NET_MASK), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $errors[] = 'Network netmask not valid';
            }
        }
        if (! Settings::get(SettingKeys::NET_GATEWAY)) {
            $errors[] = 'Network gateway not filled';
        } elseif (! filter_var(Settings::get(SettingKeys::NET_GATEWAY), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'Network gateway not valid';
        }

        return $errors;
    }

    public function test_dns()
    {
        $obj = new \StdClass;
        // env
        $env_test = self::check_env_dns();
        $obj->settings = new \StdClass;
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ['Settings formally correct'] : $env_test;
        if ($obj->settings->passed) {
            $dns1 = new DNSController(Settings::get(SettingKeys::DNS_SERVER_PRIMARY_IP), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PORT), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_USER), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PSW), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PRIVKEY), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_PATH), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_RELOAD), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_EXPORT_PLAIN));
            $obj->primary = $dns1->test();
            if (Settings::get(SettingKeys::DNS_SERVER_SECONDARY_IP)) {
                $dns2 = new DNSController(Settings::get(SettingKeys::DNS_SERVER_SECONDARY_IP), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PORT), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_USER), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PSW), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PRIVKEY), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_PATH), Settings::get(SettingKeys::DNS_SERVER_SECONDARY_RELOAD), Settings::get(SettingKeys::DNS_SERVER_PRIMARY_EXPORT_PLAIN));
                $obj->secondary = $dns2->test();
            }
        }

        return json_encode($obj);
    }

    public function test_bgp()
    {
        $obj = new \StdClass;
        // env
        $env_test = BGPController::check_env();
        $obj->settings = new \StdClass;
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ['Settings formally correct'] : $env_test;

        return json_encode($obj);
    }

    public function test_smtp()
    {
        $obj = new \StdClass;
        // env
        $env_test = self::check_env_smtp();
        $obj->settings = new \StdClass;
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ['Settings formally correct'] : $env_test;
        if ($obj->settings->passed) {
            $obj->testmail = new \StdClass;
            $to_send = ActionLogController::notify_to_send();
            try {
                Mail::send('mail.notify_error', ['system' => 'testmail', 'error' => 'testmail'],
                    function (Message $message) use ($to_send) {
                        $message->subject(config('app.name').': test mail');
                        $message->from(Settings::get(SettingKeys::MAIL_FROM_ADDRESS), Settings::get(SettingKeys::MAIL_FROM_NAME));
                        $message->to($to_send);
                    }
                );
                $obj->testmail->passed = true;
                $obj->testmail->messages = ['Mail sent to '.implode(', ', $to_send).' - please check your inbox mail'];
            } catch (\Exception $e) {
                $obj->testmail->passed = false;
                $obj->testmail->messages = ['Mail send to '.implode(', ', $to_send).' failed - '.$e->getMessage()];
            }
        }

        return json_encode($obj);
    }

    private static function validateFQDN($domain)
    {
        $pattern = "/(?=^.{1,254}$)(^(?:(?!\d+\.|-)[a-zA-Z0-9_\-]{1,63}(?<!-)\.?)+(?:[a-zA-Z]{2,})$)/";
        if (preg_match($pattern, $domain)) {
            return true;
        }

        return false;
    }

    public function log_retention()
    {
        $check_env = ActionLogController::check_env();
        if (count($check_env) == 0) {
            ActionLogController::log(0, 'log_retention_cron', 'starting run');
            $c = new ActionLogController;
            $c->log_retention();
            ActionLogController::log(0, 'log_retention_cron', 'run ended');
        } else {
            ActionLogController::log(0, 'log_retention_cron', 'run not started because of: '.implode(', ', $check_env));
        }
    }

    private static function make_network_settings_file()
    {
        ActionLogController::log(0, 'system', "trying to make network settings file in '".base_path('storage/settings/').'network.csv'."'");
        $check_env = self::check_env_network();
        if (count($check_env) == 0) {
            $content = 'IP,'.Settings::get(SettingKeys::NET_IP)."\n";
            $content .= 'MASK,'.Settings::get(SettingKeys::NET_MASK)."\n";
            $content .= 'GW,'.Settings::get(SettingKeys::NET_GATEWAY)."\n";
            try {
                file_put_contents(base_path('storage/settings/').'network.csv', $content);
                ActionLogController::log(0, 'system', "succeded to make network settings file in '".base_path('storage/settings/').'network.csv'."'");

                return true;
            } catch (\Exception $e) {
                ActionLogController::log(0, 'system', "failed to make network settings file in '".base_path('storage/settings/').'network.csv'."' (".$e->getMessage().')', true);
            }
        } else {
            ActionLogController::log(0, 'system', 'network settings file not made because of: '.implode(', ', $check_env));
        }
        return false;
    }
}
