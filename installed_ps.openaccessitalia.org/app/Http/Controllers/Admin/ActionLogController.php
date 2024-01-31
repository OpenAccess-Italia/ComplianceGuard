<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
