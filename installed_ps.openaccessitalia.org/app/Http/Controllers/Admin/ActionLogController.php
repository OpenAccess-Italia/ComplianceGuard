<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionLog;
use App\Models\Piracy\APIAccessTokens;
use App\Models\Piracy\APILog;
use App\Models\Piracy\APIRefreshTokens;
use App\SettingKeys;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Mail;
use Settings;

class ActionLogController extends Controller
{
    public static function log($user_id, $username, $action, $notify_error = false)
    {
        $log = new ActionLog;
        $log->user_id = $user_id;
        $log->username = $username;
        $log->action = $action;
        $log->save();
        if ($notify_error) {
            self::notify_error($username, $action);
        }
    }

    private static function notify_error($system, $error)
    {
        if (Settings::get(SettingKeys::MAIL_HOST) != null && Settings::get(SettingKeys::MAIL_PORT) != null && Settings::get(SettingKeys::MAIL_FROM_ADDRESS) != null && Settings::get(SettingKeys::MAIL_FROM_NAME) != null && Settings::get(SettingKeys::MAIL_TO_ADDRESSES) != null) {
            $to_send = self::notify_to_send();
            try {
                Mail::send('mail.notify_error', ['system' => $system, 'error' => $error],
                    function (Message $message) use ($to_send, $system) {
                        $message->subject(config('app.name').": $system error");
                        $message->from(Settings::get(SettingKeys::MAIL_FROM_ADDRESS), Settings::get(SettingKeys::MAIL_FROM_NAME));
                        $message->to($to_send);
                    }
                );

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    public static function notify_to_send()
    {
        date_default_timezone_set('Europe/Rome');
        $to_send = [];
        foreach (explode(',', Settings::get(SettingKeys::MAIL_TO_ADDRESSES)) as $address) {
            if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                $to_send[] = $address;
            }
        }

        return $to_send;
    }

    public static function check_env()
    {
        $errors = [];
        if (Settings::get(SettingKeys::LOGS_DAYS_ACTION) == '') {
            $errors[] = 'Action logs retention days not filled';
        } elseif (filter_var(Settings::get(SettingKeys::LOGS_DAYS_ACTION), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            $errors[] = 'Action logs retention days not valid';
        }
        if (Settings::get(SettingKeys::LOGS_DAYS_AUTHENTICATION) == '') {
            $errors[] = 'Authentication logs retention days not filled';
        } elseif (filter_var(Settings::get(SettingKeys::LOGS_DAYS_AUTHENTICATION), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            $errors[] = 'Authentication logs retention days not valid';
        }
        if (Settings::get(SettingKeys::LOGS_DAYS_PS_API) == '') {
            $errors[] = 'PiracyShield API logs retention days not filled';
        } elseif (filter_var(Settings::get(SettingKeys::LOGS_DAYS_PS_API), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            $errors[] = 'PiracyShield API logs retention days not valid';
        }
        if (Settings::get(SettingKeys::LOGS_DAYS_PS_API_ACCESS_TOKENS) == '') {
            $errors[] = 'PiracyShield API access tokens retention days not filled';
        } elseif (filter_var(Settings::get(SettingKeys::LOGS_DAYS_PS_API_ACCESS_TOKENS), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            $errors[] = 'PiracyShield API access tokens retention days not valid';
        }
        if (Settings::get(SettingKeys::LOGS_DAYS_PS_API_REFRESH_TOKENS) == '') {
            $errors[] = 'PiracyShield API refresh tokens retention days not filled';
        } elseif (filter_var(Settings::get(SettingKeys::LOGS_DAYS_PS_API_REFRESH_TOKENS), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            $errors[] = 'PiracyShield API refresh tokens retention days not valid';
        }

        return $errors;
    }

    public function log_retention()
    {
        if (Settings::get(SettingKeys::LOGS_DAYS_ACTION) > 0) {
            self::log(0, 'log_system', 'trying to execute action logs retention, max days: '.Settings::get(SettingKeys::LOGS_DAYS_ACTION));
            ActionLog::where('timestamp', '<', Carbon::now()->subDays(Settings::get(SettingKeys::LOGS_DAYS_ACTION)))->delete();
            self::log(0, 'log_system', 'succeded to execute action logs retention');
        }
        if (Settings::get(SettingKeys::LOGS_DAYS_PS_API) > 0) {
            self::log(0, 'log_system', 'trying to execute PiracyShield API logs retention, max days: '.Settings::get(SettingKeys::LOGS_DAYS_PS_API));
            APILog::where('timestamp', '<', Carbon::now()->subDays(Settings::get(SettingKeys::LOGS_DAYS_PS_API)))->delete();
            self::log(0, 'log_system', 'succeded to execute PiracyShield API logs retention');
        }
        if (Settings::get(SettingKeys::LOGS_DAYS_PS_API_ACCESS_TOKENS) > 0) {
            self::log(0, 'log_system', 'trying to execute PiracyShield API access tokens retention, max days: '.Settings::get(SettingKeys::LOGS_DAYS_PS_API_ACCESS_TOKENS));
            APIAccessTokens::where('timestamp', '<', Carbon::now()->subDays(Settings::get(SettingKeys::LOGS_DAYS_PS_API_ACCESS_TOKENS)))->delete();
            self::log(0, 'log_system', 'succeded to execute PiracyShield API access tokens retention');
        }
        if (Settings::get(SettingKeys::LOGS_DAYS_PS_API_REFRESH_TOKENS) > 0) {
            self::log(0, 'log_system', 'trying to execute PiracyShield API refresh tokens retention, max days: '.Settings::get(SettingKeys::LOGS_DAYS_PS_API_REFRESH_TOKENS));
            APIRefreshTokens::where('timestamp', '<', Carbon::now()->subDays(Settings::get(SettingKeys::LOGS_DAYS_PS_API_REFRESH_TOKENS)))->delete();
            self::log(0, 'log_system', 'succeded to execute PiracyShield API refresh tokens retention');
        }
    }
}
