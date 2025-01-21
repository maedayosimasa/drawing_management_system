<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // CORS ヘッダーの設定
        $corsHeaders = [
            'Access-Control-Allow-Origin' => 'http://localhost:5173',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ];

        // BinaryFileResponse の場合もヘッダーを設定
        if ($response instanceof BinaryFileResponse) {
            foreach ($corsHeaders as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        }

        // 通常のレスポンスにもヘッダーを設定
        foreach ($corsHeaders as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}

