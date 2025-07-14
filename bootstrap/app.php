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
    ->withMiddleware(function (Middleware $middleware) {
        // ここにミドルウェアを登録
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class, // ★この行を追加
        ]);

        // 必要であれば、グローバルミドルウェアやグループミドルウェアもここで設定
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();