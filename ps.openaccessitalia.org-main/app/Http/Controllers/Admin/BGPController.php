<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BGPController extends Controller
{
    public function make_ipv4_list_file()
    {
        ActionLogController::log(0, 'bgp_system', "trying to make ipv4 bgp file in '".base_path('storage/download/').'ipv4.txt'."'");
        $content = '';
        $ipv4s_piracy = \App\Models\Piracy\IPv4s::select('ipv4')->distinct()->pluck('ipv4')->toArray();
        $ipv4s_manual = \App\Models\Manual\IPv4s::select('ipv4')->distinct()->pluck('ipv4')->toArray();
        $done = [];
        foreach ($ipv4s_piracy as $ipv4) {
            if (! in_array($ipv4, $done)) {
                $content .= "$ipv4\n";
                $done[] = $ipv4;
            }
        }
        foreach ($ipv4s_manual as $ipv4) {
            if (! in_array($ipv4, $done)) {
                $content .= "$ipv4\n";
                $done[] = $ipv4;
            }
        }
        try {
            file_put_contents(base_path('storage/download/').'ipv4.txt', $content);
            ActionLogController::log(0, 'bgp_system', "succeded to make ipv4 bgp file in '".base_path('storage/download/').'ipv4.txt'."'");

            return true;
        } catch (\Exception $e) {
            ActionLogController::log(0, 'bgp_system', "failed to make ipv4 bgp file in '".base_path('storage/download/').'ipv4.txt'."' (".$e->getMessage().')', true);

            return false;
        }
    }

    public function make_ipv6_list_file()
    {
        ActionLogController::log(0, 'bgp_system', "trying to make ipv6 bgp file in '".base_path('storage/download/').'ipv6.txt'."'");
        $content = '';
        $ipv6s_piracy = \App\Models\Piracy\IPv6s::select('ipv6')->distinct()->pluck('ipv6')->toArray();
        $ipv6s_manual = \App\Models\Manual\IPv6s::select('ipv6')->distinct()->pluck('ipv6')->toArray();
        $done = [];
        foreach ($ipv6s_piracy as $ipv6) {
            if (! in_array($ipv6, $done)) {
                $content .= "$ipv6\n";
                $done[] = $ipv6;
            }
        }
        foreach ($ipv6s_manual as $ipv6) {
            if (! in_array($ipv6, $done)) {
                $content .= "$ipv6\n";
                $done[] = $ipv6;
            }
        }
        try {
            file_put_contents(base_path('storage/download/').'ipv6.txt', $content);
            ActionLogController::log(0, 'bgp_system', "succeded to make ipv6 bgp file in '".base_path('storage/download/').'ipv6.txt'."'");

            return true;
        } catch (\Exception $e) {
            ActionLogController::log(0, 'bgp_system', "failed to make ipv6 bgp file in '".base_path('storage/download/').'ipv6.txt'."' (".$e->getMessage().')', true);

            return false;
        }
    }

    public static function make_settings_file()
    {
        ActionLogController::log(0, 'bgp_system', "trying to make bgp settings file in '".base_path('storage/settings/').'network.csv'."'");
        $check_env = self::check_env();
        if (count($check_env) == 0) {
            $content = 'NEI,'.env('BGP_ROUTER_IP')."\n";
            $content .= 'AS,'.env('BGP_ASN')."\n";
            $content .= 'IP,'.env('BGP_LOCAL_IP')."\n";
            $content .= 'MASK,'.env('BGP_LOCAL_MASK')."\n";
            $content .= 'GW,'.env('BGP_LOCAL_GATEWAY')."\n";
            $content .= 'NS,'.env('EXTERNAL_DNS_SERVERS')."\n";
            try {
                file_put_contents(base_path('storage/settings/').'bgp.csv', $content);
                ActionLogController::log(0, 'bgp_system', "succeded to make bgp settings file in '".base_path('storage/settings/').'bgp.csv'."'");

                return true;
            } catch (\Exception $e) {
                ActionLogController::log(0, 'bgp_system', "failed to make bgp settings file in '".base_path('storage/settings/').'bgp.csv'."' (".$e->getMessage().')', true);

                return false;
            }
        } else {
            ActionLogController::log(0, 'bgp_system', 'network bgp file not made because of: '.implode(', ', $check_env));
        }

        return false;
    }

    public static function check_env()
    {
        $errors = [];
        if (! env('BGP_ROUTER_IP')) {
            $errors[] = 'BGP router IP not filled';
        } elseif (! filter_var(env('BGP_ROUTER_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'BGP router IP not valid';
        }
        if (! env('BGP_ASN')) {
            $errors[] = 'ASN not filled';
        } elseif (! is_numeric(env('BGP_ASN'))) {
            $errors[] = 'ASN not valid';
        }
        if (! env('BGP_LOCAL_IP')) {
            $errors[] = 'BGP router IP not filled';
        } elseif (! filter_var(env('BGP_LOCAL_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'BGP local IP not valid';
        }
        if (! env('BGP_LOCAL_MASK')) {
            $errors[] = 'BGP router IP not filled';
        } elseif (! filter_var(env('BGP_LOCAL_MASK'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'BGP mask not valid';
        }
        if (! env('BGP_LOCAL_GATEWAY')) {
            $errors[] = 'BGP router IP not filled';
        } elseif (! filter_var(env('BGP_LOCAL_GATEWAY'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'BGP gateway not valid';
        }

        return $errors;
    }
}
