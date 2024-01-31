<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //UPDATE CNCPO BLACKLIST
        $schedule->call('App\Http\Controllers\CNCPOController@update_blacklist')->timezone('Europe/Rome')->dailyAt('10:00');
        //UPDATE ADM BLACKLISTS
        $schedule->call('App\Http\Controllers\ADMController@update_blacklists')->timezone('Europe/Rome')->dailyAt('9:00');
        //UPDATE PIRACY SHIELD
        $schedule->call('App\Http\Controllers\PiracyController@run')->timezone('Europe/Rome')->everyTenMinutes();
        //UPDATE DNS
        $schedule->call('App\Http\Controllers\Admin\AdminController@update_dns')->timezone('Europe/Rome')->everyTenMinutes();
        //UPDATE BGP
        $schedule->call('App\Http\Controllers\Admin\AdminController@update_bgp')->timezone('Europe/Rome')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
