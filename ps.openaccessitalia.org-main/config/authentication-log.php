<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notify New Device
    |--------------------------------------------------------------------------
    |
    | Here you define whether to receive notifications when logging from a new device.
    |
    */

    'notify' => env('AUTHENTICATION_LOG_NOTIFY', false),

    /*
    |--------------------------------------------------------------------------
    | Old Logs Clear
    |--------------------------------------------------------------------------
    |
    | When the clean-command is executed, all authentication logs older than
    | the number of days specified here will be deleted.
    |
    */

    'older' => env('LOGS_DAYS_AUTHENTICATION', 365),

];
