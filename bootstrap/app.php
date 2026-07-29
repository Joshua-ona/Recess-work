<?php

putenv('MAIL_HOST');
putenv('MAIL_USERNAME');
putenv('MAIL_PASSWORD');

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'redirect.active.quiz' => \App\Http\Middleware\RedirectToActiveQuiz::class,
        ]);

        // Stamps last_active_at on every authenticated web request — this
        // feeds the "Daily active users" charts on the admin dashboard and
        // analytics page. It was defined but never actually registered
        // anywhere, so last_active_at never updated.
        $middleware->web(append: [
            \App\Http\Middleware\TrackLastActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();