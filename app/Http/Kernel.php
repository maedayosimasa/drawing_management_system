<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
         \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,// グローバルミドルウェア
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\CorsMiddleware::class,
        \App\Http\Middleware\HandleLargeRequests::class, // カスタムミドルウェアを追加サイズをチェックしない

        'cors' => \App\Http\Middleware\Cors::class, // ここに追加
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \Fruitcake\Cors\HandleCors::class,
            //\App\Http\Middleware\Cors::class, // ★追加
           \App\Http\Middleware\CorsMiddleware::class, // ★追加
           // \App\Http\Middleware\CORS::class,  // 'web'グループにCORSミドルウェアを追加
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\CorsMiddleware::class,
            'cors' => \App\Http\Middleware\Cors::class,// ここに追加
        ],
    ];
    
    protected $routeMiddleware = [
        // 他のミドルウェア
        'cors' => \App\Http\Middleware\Cors::class,//ここに追加
    ];

    
}


