<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\Localization;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /**
         * Append the Localization middleware to the global middleware stack
         */
        // $middleware->append(Localization::class);
        $middleware->web(append: [Localization::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
