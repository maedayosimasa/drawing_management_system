public function handle($request, Closure $next)
{
$response = $next($request);

// ストレージからのリクエストにCORSヘッダーを追加
if ($request->is('storage/*')) {
$response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
$response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
}

return $response;
}