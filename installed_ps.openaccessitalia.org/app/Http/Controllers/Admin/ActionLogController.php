<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ActionLogController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function log($user_id,$username,$action,$notify_error = false){
        $log = new \App\ActionLog();
        $log->user_id = $user_id;
        $log->username = $username;
        $log->action = $action;
        $log->save();
        if($notify_error){
            self::notify_error($username,$action);
        }
    }

    private static function notify_error($system,$error){
        if(env('MAIL_HOST') != null && env('MAIL_PORT') != null && env('MAIL_FROM_ADDRESS') != null && env('MAIL_FROM_NAME') != null && env('MAIL_TO_ADDRESSES') != null){
            $to_send = self::notify_to_send();
            try {
                \Mail::send('mail.notify_error',['system' => $system,'error' => $error],
                    function (\Illuminate\Mail\Message $message) use ($to_send,$system){
	            	    $message->subject(env('APP_NAME').": $system error");
	            	    $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
                        $message->to($to_send);
                    }
                );
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    public static function notify_to_send(){
        date_default_timezone_set('Europe/Rome');
        $to_send = [];
        $raw_to_send = explode(",",env('MAIL_TO_ADDRESSES'));
        foreach ($raw_to_send as $address) {
            if(filter_var($address,FILTER_VALIDATE_EMAIL)){
                $to_send[] = $address;
            }
        }
        return $to_send;
    }

    public static function check_env(){
        $errors = [];
        if(env('LOGS_DAYS_ACTION') == ""){
            $errors[] = "Action logs retention days not filled";
        }else{
            if(filter_var(env('LOGS_DAYS_ACTION'), FILTER_VALIDATE_INT, array("options" => array("min_range"=>0))) === false){
                $errors[] = "Action logs retention days not valid";
            }
        }
        if(env('LOGS_DAYS_AUTHENTICATION') == ""){
            $errors[] = "Authentication logs retention days not filled";
        }else{
            if(filter_var(env('LOGS_DAYS_AUTHENTICATION'), FILTER_VALIDATE_INT, array("options" => array("min_range"=>0))) === false){
                $errors[] = "Authentication logs retention days not valid";
            }
        }
        if(env('LOGS_DAYS_PS_API') == ""){
            $errors[] = "PiracyShield API logs retention days not filled";
        }else{
            if(filter_var(env('LOGS_DAYS_PS_API'), FILTER_VALIDATE_INT, array("options" => array("min_range"=>0))) === false){
                $errors[] = "PiracyShield API logs retention days not valid";
            }
        }
        if(env('LOGS_DAYS_PS_API_ACCESS_TOKENS') == ""){
            $errors[] = "PiracyShield API access tokens retention days not filled";
        }else{
            if(filter_var(env('LOGS_DAYS_PS_API_ACCESS_TOKENS'), FILTER_VALIDATE_INT, array("options" => array("min_range"=>0))) === false){
                $errors[] = "PiracyShield API access tokens retention days not valid";
            }
        }
        if(env('LOGS_DAYS_PS_API_REFRESH_TOKENS') == ""){
            $errors[] = "PiracyShield API refresh tokens retention days not filled";
        }else{
            if(filter_var(env('LOGS_DAYS_PS_API_REFRESH_TOKENS'), FILTER_VALIDATE_INT, array("options" => array("min_range"=>0))) === false){
                $errors[] = "PiracyShield API refresh tokens retention days not valid";
            }
        }
        return $errors;
    }

    public function log_retention(){
        if (env("LOGS_DAYS_ACTION") > 0){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","trying to execute action logs retention, max days: ".env("LOGS_DAYS_ACTION"));
            \App\ActionLog::where('timestamp', '<', Carbon::now()->subDay(env("LOGS_DAYS_ACTION")))->delete();
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","succeded to execute action logs retention");
        }
        if (env("LOGS_DAYS_PS_API") > 0){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","trying to execute PiracyShield API logs retention, max days: ".env("LOGS_DAYS_PS_API"));
            \App\Piracy\APILog::where('timestamp', '<', Carbon::now()->subDay(env("LOGS_DAYS_PS_API")))->delete();
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","succeded to execute PiracyShield API logs retention");
        }
        if (env("LOGS_DAYS_PS_API_ACCESS_TOKENS") > 0){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","trying to execute PiracyShield API access tokens retention, max days: ".env("LOGS_DAYS_PS_API_ACCESS_TOKENS"));
            \App\Piracy\APIAccessTokens::where('timestamp', '<', Carbon::now()->subDay(env("LOGS_DAYS_PS_API_ACCESS_TOKENS")))->delete();
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","succeded to execute PiracyShield API access tokens retention");
        }
        if (env("LOGS_DAYS_PS_API_REFRESH_TOKENS") > 0){
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","trying to execute PiracyShield API refresh tokens retention, max days: ".env("LOGS_DAYS_PS_API_REFRESH_TOKENS"));
            \App\Piracy\APIRefreshTokens::where('timestamp', '<', Carbon::now()->subDay(env("LOGS_DAYS_PS_API_REFRESH_TOKENS")))->delete();
            \App\Http\Controllers\Admin\ActionLogController::log(0,"log_system","succeded to execute PiracyShield API refresh tokens retention");
        }
    }
}
