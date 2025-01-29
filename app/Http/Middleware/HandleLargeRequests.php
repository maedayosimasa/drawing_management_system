<?php 


namespace App\Http\Middleware;

use Closure;

class HandleLargeRequests
{
    public function handle($request, Closure $next)
    {
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('memory_limit', '256M'); // 必要なら調整
        // ini_set('post_max_size', '0');//無制限
        // ini_set('upload_max_filesize', '0');//無制限
        return $next($request);
    }
}
