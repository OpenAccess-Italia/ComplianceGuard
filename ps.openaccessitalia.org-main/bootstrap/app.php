<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\IsAdmin::class,
            'auth.piracy' => \App\Http\Middleware\CanPiracy::class,
            'auth.cncpo' => \App\Http\Middleware\CanCNCPO::class,
            'auth.adm' => \App\Http\Middleware\CanADM::class,
            'auth.manual' => \App\Http\Middleware\CanManual::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })
    ->withSchedule(function (Schedule $schedule) {
        // UPDATE CNCPO BLACKLIST
        $schedule->call('App\Http\Controllers\CNCPOController@update_blacklist')->timezone('Europe/Rome')->dailyAt('10:00');
        // UPDATE ADM BLACKLISTS
        $schedule->call('App\Http\Controllers\ADMController@update_blacklists')->timezone('Europe/Rome')->dailyAt('9:00');
        // UPDATE PIRACY SHIELD
        $schedule->call('App\Http\Controllers\PiracyController@run')->timezone('Europe/Rome')->everyTenMinutes();
        // UPDATE DNS
        $schedule->call('App\Http\Controllers\Admin\AdminController@update_dns')->timezone('Europe/Rome')->everyTenMinutes();
        // UPDATE BGP
        $schedule->call('App\Http\Controllers\Admin\AdminController@update_bgp')->timezone('Europe/Rome')->everyTenMinutes();
        // LOG RETENTION
        $schedule->call('App\Http\Controllers\Admin\AdminController@log_retention')->timezone('Europe/Rome')->hourly();
    })
    ->create();
