<?php

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
    })->create();
