<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\DynamicDBConnection;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\InternalAccessOnly;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'auth404' => \App\Http\Middleware\Auth404::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);


    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
