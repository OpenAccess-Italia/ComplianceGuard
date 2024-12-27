<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpseclib\Net\SSH2;
use DB;

class DNSController extends Controller
{
    //
    private $ip,$user,$psw,$path,$reload;

    public function __construct($ip,$port,$user,$psw,$path,$reload)
    {
        $this->middleware('auth.admin');
        $this->ip = $ip;
        $this->port = $port;
        $this->user = $user;
        $this->psw = $psw;
        $this->path = $path;
        $this->reload = $reload;
    }

    private function connect(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","trying to connect via ssh to $this->ip (port $this->port)");
        try{
            $ssh_connection = \ssh2_connect($this->ip,$this->port);
            if(\ssh2_auth_password($ssh_connection,$this->user,$this->psw)){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","connected via ssh to $this->ip (port $this->port)");
                return $ssh_connection;
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to connect via ssh to $this->ip (port $this->port) (auth failed)",true);
                return false;
            }
        }catch(\Exception $e){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to connect via ssh to $this->ip (port $this->port) (".$e->getMessage().")",true);
            return false;
        }
        
    }

    private static function make_db_content($ip){
        $content = <<<EOD
\$TTL 3D
@       IN      SOA     localhost. root.localhost. (
                1       ;
                8H      ;
                2H      ;
                1W      ;
                1D      );
        IN      NS      localhost.

        IN      A       $ip    

EOD;
        return $content;
    }

    private function make_zone_record($dns,$db_filename){
        $content = <<<EOD
zone "$dns" {
            type master ;
            file "$db_filename" ;
        } ;

EOD;
        return $content;
    }

    private function sftp_file_exists($filename){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","trying to check if $filename exists in DNS server $this->ip");
        $ssh_connection = $this->connect();
        if($ssh_connection){
            $sftp = ssh2_sftp($ssh_connection);
            $fileExists = file_exists('ssh2.sftp://' . $sftp . '/'.$filename);
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","succeded to check if $filename exists in DNS server $this->ip ($fileExists)");
            return $fileExists;
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to check if $filename exists in DNS server $this->ip (fail to connect)",true);
            return false;
        }     
    }

    private function sftp_read_file($filename){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","trying to read $filename in DNS server $this->ip");
        $ssh_connection = $this->connect();
        if($ssh_connection){
            $sftp = ssh2_sftp($ssh_connection);
            if($sftp){
                try{
                    $content = stream_get_contents(fopen('ssh2.sftp://' . $sftp . '/'.$filename,'r'));
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","succeded to read $filename in DNS server $this->ip");
                    return $content;
                }catch(\Exception $e){
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to read $filename in DNS server $this->ip (".$e->getMessage().")",true);
                    return false;
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to read $filename in DNS server $this->ip (can't establish sftp)",true);
                return false;
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to read $filename in DNS server $this->ip (fail to connect)",true);
            return false;
        }
    }

    private function sftp_write_file($filename,$content){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","trying to write $filename in DNS server $this->ip");
        $ssh_connection = $this->connect();
        if($ssh_connection){
            $sftp = ssh2_sftp($ssh_connection);
            if($sftp){
                try{
                    $stream = fopen('ssh2.sftp://' . $sftp . '/'.$filename,'w');
                    fwrite($stream,$content);
                    fclose($stream);
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","succeded to write $filename in DNS server $this->ip");
                    return true;
                }catch(\Exception $e){
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to write $filename in DNS server $this->ip (".$e->getMessage().")",true);
                    return false;
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to write $filename in DNS server $this->ip (can't establish sftp)",true);
                return false;
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to write $filename in DNS server $this->ip (fail to connect)",true);
            return false;
        }
        
    }

    private static function zones_directory($path){
        $arr_path = explode("/",$path);
        array_pop($arr_path);
        return join("/",$arr_path);
    }

    private function install_dbs(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","beginning to check if all dns record dbs exists in DNS server $this->ip");
        $directory = self::zones_directory($this->path);
        //CNCPO -  db.cncpoblocked
        $cncpo_db_content = self::make_db_content(env('CNCPO_DNS_REDIRECT_IP','127.0.0.1'));
        if($this->sftp_file_exists($directory."/db.cncpoblocked")){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.cncpoblocked already exists in DNS server $this->ip");
            if($this->sftp_read_file($directory."/db.cncpoblocked") != $cncpo_db_content){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.cncpoblocked is not updated in DNS server $this->ip");
                $this->sftp_write_file($directory."/db.cncpoblocked",$cncpo_db_content);
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.cncpoblocked is updated in DNS server $this->ip");
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.cncpoblocked not exists in DNS server $this->ip");
            $this->sftp_write_file($directory."/db.cncpoblocked",$cncpo_db_content);
        }
        //ADM - db.admblocked
        $adm_db_content = self::make_db_content(env('ADM_DNS_REDIRECT_IP','127.0.0.1'));
        if($this->sftp_file_exists($directory."/db.admblocked")){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.admblocked already exists in DNS server $this->ip");
            if($this->sftp_read_file($directory."/db.admblocked") != $adm_db_content){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.admblocked is not updated in DNS server $this->ip");
                $this->sftp_write_file($directory."/db.admblocked",$adm_db_content);
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.admblocked is updated in DNS server $this->ip");
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.admblocked not exists in DNS server $this->ip");
            $this->sftp_write_file($directory."/db.admblocked",$adm_db_content);
        }
        //PIRACY - db.psblocked
        $ps_db_content = self::make_db_content(env('PIRACY_SHIELD_DNS_REDIRECT_IP','127.0.0.1'));
        if($this->sftp_file_exists($directory."/db.psblocked")){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.psblocked already exists in DNS server $this->ip");
            if($this->sftp_read_file($directory."/db.psblocked") != $ps_db_content){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.psblocked is not updated in DNS server $this->ip");
                $this->sftp_write_file($directory."/db.psblocked",$ps_db_content);
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.psblocked is updated in DNS server $this->ip");
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.psblocked not exists in DNS server $this->ip");
            $this->sftp_write_file($directory."/db.psblocked",$ps_db_content);
        }
        //MANUAL - db.manblocked
        $man_db_content = self::make_db_content(env('MANUAL_DNS_REDIRECT_IP','127.0.0.1'));
        if($this->sftp_file_exists($directory."/db.manblocked")){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.manblocked already exists in DNS server $this->ip");
            if($this->sftp_read_file($directory."/db.manblocked") != $man_db_content){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.manblocked is not updated in DNS server $this->ip");
                $this->sftp_write_file($directory."/db.manblocked",$man_db_content);
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.manblocked is updated in DNS server $this->ip");
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","db.manblocked not exists in DNS server $this->ip");
            $this->sftp_write_file($directory."/db.manblocked",$man_db_content);
        }
    }

    private function install_zone(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","beginning to make dns zone content in DNS server $this->ip");
        $content = '';
        $directory = self::zones_directory($this->path);
        $admbettingblacklist = \App\ADM\BettingBlacklist::select('fqdn')->distinct()->pluck('fqdn')->toArray();
        $admsmokingblacklist = \App\ADM\SmokingBlacklist::select('fqdn')->distinct()->pluck('fqdn')->toArray();
        $cncpoblacklist = \App\CNCPO\Blacklist::select('fqdn')->distinct()->pluck('fqdn')->toArray();
        $months = env('PIRACY_SHIELD_ITEMS_VALIDITY_MONTHS');
        $piracyshield = collect(DB::connection('mysql')->select("select feedbacks.item from (select distinct item from ps_ticket_items_log where item_type = 'fqdn' and `timestamp` > DATE_SUB(now(), INTERVAL ? MONTH)) as feedbacks INNER JOIN (select fqdn as item from ps_fqdns where `timestamp` > DATE_SUB(now(), INTERVAL ? MONTH)) as lastitems on feedbacks.item = lastitems.item order by item",[$months,$months]))->pluck('item');
        $manual = \App\Manual\FQDNs::select('fqdn')->distinct()->pluck('fqdn')->toArray();
        $done = [];
        foreach ($admbettingblacklist as $fqdn) {
            if(!in_array($fqdn,$done)){
                $content .= self::make_zone_record($fqdn,$directory."/db.admblocked");
                $done[] = $fqdn;
            }
        }
        foreach ($admsmokingblacklist as $fqdn) {
            if(!in_array($fqdn,$done)){
                $content .= self::make_zone_record($fqdn,$directory."/db.admblocked");
                $done[] = $fqdn;
            }
        }
        foreach ($cncpoblacklist as $fqdn) {
            if(!in_array($fqdn,$done)){
                $content .= self::make_zone_record($fqdn,$directory."/db.cncpoblocked");
                $done[] = $fqdn;
            }
        }
        foreach ($piracyshield as $fqdn) {
            if(!in_array($fqdn,$done)){
                $content .= self::make_zone_record($fqdn,$directory."/db.psblocked");
                $done[] = $fqdn;
            }
        }
        foreach ($manual as $fqdn) {
            if(!in_array($fqdn,$done)){
                $content .= self::make_zone_record($fqdn,$directory."/db.manblocked");
                $done[] = $fqdn;
            }
        }
        if($this->sftp_read_file($this->path) != $content){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","$this->path is not updated in DNS server $this->ip");
            if($this->sftp_write_file($this->path,$content)){
                $need_reload = true;
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","succeded to make dns zone content in DNS server $this->ip (reload needed)");
            }else{
                $need_reload = false;
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","failed to make dns zone content in DNS server $this->ip (reload not needed)");
            }
        }else{
            $need_reload = false;
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","$this->path is already updated in DNS server $this->ip (reload not needed)");
        }
        return $need_reload;
    }

    private function reload_service(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","trying to execute command '$this->reload' in DNS server $this->ip");
        $ssh_connection = $this->connect();
        if($ssh_connection){
            try{
                $stream = ssh2_exec($ssh_connection,$this->reload);
                stream_set_blocking($stream, true);
                $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
                $data = stream_get_contents($stream_out);
                $ssh_connection = null;
                if(trim($data) == ""){
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","succeded to execute command '$this->reload' in DNS server $this->ip");
                    return true;
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to execute command '$this->reload' in DNS server $this->ip ($data)",true);
                    return false;
                }
            }catch(\Exception $e){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to execute command '$this->reload' in DNS server $this->ip (".$e->getMessage().")",true);
                return false;
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_system","failed to execute command '$this->reload' in DNS server $this->ip (fail to connect)",true);
            return false;
        }
    }

    public function update(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","starting run for DNS server $this->ip");
        $this->install_dbs();
        if($this->install_zone()){
            if($this->reload_service()){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","dns service in DNS server $this->ip reloaded");
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","dns service in DNS server $this->ip failed to send reload");
            }
        }
        \App\Http\Controllers\Admin\ActionLogController::log(0,"dns_cron","run ended for DNS server $this->ip");
    }

    public function test(){
        $obj = new \StdClass();
        //connection
        $obj->connection = new \StdClass();
        $connection = self::connect();
        if($connection !== false){
            $obj->connection->passed = true;
            $obj->connection->messages = ["SSH connection succeded"];
        }else{
            $obj->connection->passed = false;
            $obj->connection->messages = ["SSH connection faled (view action log for more infos)"];
        }
        if($obj->connection->passed){
            $path_infos = pathinfo($this->path);
            $dir = $path_infos["dirname"];
            //write privileges
            $obj->write = new \StdClass();
            if(self::sftp_write_file("$dir/dummy","dummy")){
                $obj->write->passed = true;
                $obj->write->messages = ["SSH user has write privileges in directory $dir"];
            }else{
                $obj->write->passed = false;
                $obj->write->messages = ["Write failed in directory $dir (view action log for more infos)"];
            }
            if($obj->write->passed){
                //read privileges
                $obj->read = new \StdClass();
                if(self::sftp_read_file("$dir/dummy") === "dummy"){
                    $obj->read->passed = true;
                    $obj->read->messages = ["SSH user has read privileges in directory $dir"];
                }else{
                    $obj->read->passed = false;
                    $obj->read->messages = ["Read failed in directory $dir (view action log for more infos)"];
                }
                $sftp = ssh2_sftp($connection);
                ssh2_sftp_unlink($sftp,"$dir/dummy");
            }
            //service reload
            $obj->reload = new \StdClass();
            if(self::reload_service()){
                $obj->reload->passed = true;
                $obj->reload->messages = ["Service reload success ($this->reload)"];
            }else{
                $obj->reload->passed = false;
                $obj->reload->messages = ["Service reload failed ($this->reload) (view action log for more infos)"];
            }
        }
        return $obj;
    }

}