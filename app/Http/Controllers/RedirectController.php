<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function redirectToReactPage()
    {
        // ReactアプリのURLを設定
        $reactUrl = 'http://127.0.0.1:3000/target-page';

        // リダイレクトを実行
        return response()->json([
            'redirect_url' => $reactUrl,
        ]);
    }
}
