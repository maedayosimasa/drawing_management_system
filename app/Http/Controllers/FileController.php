<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function download($fileName)
    {
        // ストレージからファイルを取得
        if (Storage::exists('public/' . $fileName)) {
            return Storage::download('public/' . $fileName);
        }

        // ファイルが存在しない場合は404を返す
        return response()->json(['error' => 'File not found'], 404);
    }
}
