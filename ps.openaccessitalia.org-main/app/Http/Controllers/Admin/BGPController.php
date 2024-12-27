<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class BGPController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    public function make_ipv4_list_file(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","trying to make ipv4 bgp file in '".base_path('storage/download/').'ipv4.txt'."'");
        $content = '';
        $months = env('PIRACY_SHIELD_ITEMS_VALIDITY_MONTHS');
        $ipv4s_piracy = collect(DB::connection('mysql')->select("select feedbacks.item from (select distinct item from ps_ticket_items_log where item_type = 'ipv4' and `timestamp` > DATE_SUB(now(), INTERVAL ? MONTH)) as feedbacks INNER JOIN (select ipv4 as item from ps_ipv4s where `timestamp` > DATE_SUB(now(), INTERVAL ? MONTH)) as lastitems on feedbacks.item = lastitems.item order by item",[$months,$months]))->pluck('item');
        $ipv4s_manual = \App\Manual\IPv4s::select('ipv4')->distinct()->pluck('ipv4')->toArray();
        $done = [];
        foreach ($ipv4s_piracy as $ipv4) {
            if(!in_array($ipv4,$done)){
                $content .= "$ipv4\n";
                $done[] = $ipv4;
            }
        }
        foreach ($ipv4s_manual as $ipv4) {
            if(!in_array($ipv4,$done)){
                $content .= "$ipv4\n";
                $done[] = $ipv4;
            }
        }
        try{
            file_put_contents(base_path('storage/download/').'ipv4.txt',$content);
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","succeded to make ipv4 bgp file in '".base_path('storage/download/').'ipv4.txt'."'");
            return true;
        }catch(\Exception $e){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","failed to make ipv4 bgp file in '".base_path('storage/download/').'ipv4.txt'."' (".$e->getMessage().")",true);
            return false;
        }
    }

    public function make_ipv6_list_file(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","trying to make ipv6 bgp file in '".base_path('storage/download/').'ipv6.txt'."'");
        $content = '';
        $months = env('PIRACY_SHIELD_ITEMS_VALIDITY_MONTHS');
        $ipv6s_piracy = collect(DB::connection('mysql')->select("select feedbacks.item from (select distinct item from ps_ticket_items_log where item_type = 'ipv6' and `timestamp` > DATE_SUB(now(), INTERVAL ? MONTH)) as feedbacks INNER JOIN (select ipv6 as item from ps_ipv6s where `timestamp` > DATE_SUB(now(), INTERVAL ? MONTH)) as lastitems on feedbacks.item = lastitems.item order by item",[$months,$months]))->pluck('item');
        $ipv6s_manual = \App\Manual\IPv6s::select('ipv6')->distinct()->pluck('ipv6')->toArray();
        $done = [];
        foreach ($ipv6s_piracy as $ipv6) {
            if(!in_array($ipv6,$done)){
                $content .= "$ipv6\n";
                $done[] = $ipv6;
            }
        }
        foreach ($ipv6s_manual as $ipv6) {
            if(!in_array($ipv6,$done)){
                $content .= "$ipv6\n";
                $done[] = $ipv6;
            }
        }
        try{
            file_put_contents(base_path('storage/download/').'ipv6.txt',$content);
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","succeded to make ipv6 bgp file in '".base_path('storage/download/').'ipv6.txt'."'");
            return true;
        }catch(\Exception $e){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","failed to make ipv6 bgp file in '".base_path('storage/download/').'ipv6.txt'."' (".$e->getMessage().")",true);
            return false;
        }
    }

    public static function make_settings_file(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","trying to make bgp settings file in '".base_path('storage/settings/').'network.csv'."'");
        $check_env = self::check_env();
        if(count($check_env) == 0){
            $content = '';
            $content .= 'NEI,'.env('BGP_ROUTER_IP')."\n";
            $content .= 'AS,'.env('BGP_ASN')."\n";
            $content .= 'IP,'.env('BGP_LOCAL_IP')."\n";
            $content .= 'MASK,'.env('BGP_LOCAL_MASK')."\n";
            $content .= 'GW,'.env('BGP_LOCAL_GATEWAY')."\n";
            $content .= 'NS,'.env('EXTERNAL_DNS_SERVERS')."\n";
            try{
                file_put_contents(base_path('storage/settings/').'bgp.csv',$content);
                \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","succeded to make bgp settings file in '".base_path('storage/settings/').'bgp.csv'."'");
                return true;
            }catch(\Exception $e){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","failed to make bgp settings file in '".base_path('storage/settings/').'bgp.csv'."' (".$e->getMessage().")",true);
                return false;
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"bgp_system","network bgp file not made because of: ".implode(", ",$check_env));
        }
    }

    public static function check_env(){
        $errors = [];
        if(!env('BGP_ROUTER_IP')){
            $errors[] = "BGP router IP not filled";
        }else{
            if(!filter_var(env('BGP_ROUTER_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "BGP router IP not valid";
            }
        }
        if(!env('BGP_ASN')){
            $errors[] = "ASN not filled";
        }else{
            if(!is_numeric(env('BGP_ASN'))){
                $errors[] = "ASN not valid";
            }
        }
        if(!env('BGP_LOCAL_IP')){
            $errors[] = "BGP router IP not filled";
        }else{
            if(!filter_var(env('BGP_LOCAL_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "BGP local IP not valid";
            }
        }
        if(!env('BGP_LOCAL_MASK')){
            $errors[] = "BGP router IP not filled";
        }else{
            if(!filter_var(env('BGP_LOCAL_MASK'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "BGP mask not valid";
            }
        }
        if(!env('BGP_LOCAL_GATEWAY')){
            $errors[] = "BGP router IP not filled";
        }else{
            if(!filter_var(env('BGP_LOCAL_GATEWAY'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "BGP gateway not valid";
            }
        }
        return $errors;
    }

}
