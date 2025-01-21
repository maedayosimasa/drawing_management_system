<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */
    //Ractの設定
    'paths' => ['api/*','sanctum/csrf-cookie', 'storage/*'], // CORSを許可するパスを指定
    'allowed_methods' => ['*'], // 許可するHTTPメソッド（GET, POST, etc.）
    'allowed_origins' => ['*'],
    //'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173')), // Reactアプリのオリジン
    //'allowed_origins' => ['http://localhost:5173'], // 許可するオリジン
    'allowed_origins_patterns' => [], // 特定のパターンを許可
    'allowed_headers' => ['*'], // 許可するリクエストヘッダー
    'exposed_headers' => ['Content-Disposition'], // レスポンスで公開するヘッダー
    'max_age' => 0, // プリフライトリクエストのキャッシュ時間
    'supports_credentials' =>  false, // クレデンシャル（Cookieなど）を許可するかどうか

    //'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')), // 環境変数から取得


    'allowed_origins_patterns' => [], // 正規表現で許可するオリジンを指定

    'Access-Control-Allow-Origin' => ['http://localhost:5173'],


  
];
