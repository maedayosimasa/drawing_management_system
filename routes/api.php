<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\project_name;
use App\Http\Controllers\Project_nameController;
use Illuminate\Support\Facades\Log; // Logクラスをインポート
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\FileController;
use App\Http\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//RactとのAPIのルート設定
// Route::get('/project_names', function () {
//     $project_names = project_name::witexit
//         // 必要なリレーションを含むプロジェクト名を取得
//         'drawing.design_drawing',
//         'drawing.structural_diagram',
//         'drawing.equipment_diagram',
//         'drawing.bim_drawing',
//         'meeting_log'
//     )->get();
//     return response()->json($project_names); // JSONでデータを返す
// });

//RactとのAPIのルート設定
Route::get('Project_name/index', [Project_nameController::class, 'index']);
Route::post('Project_name', [Project_nameController::class, 'store']);

//upLoad,downLoadルート設定
 Route::post('Project_name/upload', [Project_nameController::class, 'upload'])->name('upload');
 Route::get('Project_name/downLoad', [Project_nameController::class, 'downLoad'])->name('downLoad');

//createのルート設定
Route::post('Project_name', [Project_nameController::class, 'upload'])->name('upload');
//Route::get('Project_name', [Project_nameController::class, 'download'])->name('download');

//seachのルート設定
Route::post('Project_name/search', [Project_nameController::class, 'search'])->name('search');
Route::get('Project_name/select', [Project_nameController::class, 'select'])->name('select');

//showのルート設定
Route::post('Project_name/show', [Project_nameController::class, 'show'])->name('show');
Route::get('Project_name/show/{id}', [Project_nameController::class, 'show'])->name('show');

//extraction
Route::get('Project_name/extraction/{id}', [Project_nameController::class, 'extraction'])->name('extraction');


//selectのルート設定
 Route::post('Project_name/select', [Project_nameController::class, 'select'])->name('project_name.select');
 Route::get('Project_name/select', [Project_nameController::class, 'select'])->name('project_name.select');

//updateの設定
//
Route::post('Project_name/update', [Project_nameController::class, 'update'])->name('update');
//Route::get('Project_name/update', [Project_nameController::class, 'update'])->name('update');

Route::options('{any}', function () {
    return response()->json([]);
})->where('any', '.*');

Route::get('/api/Project_name/download/{file}', function ($file) {
    $path = storage_path("app/public/uploads/{$file}");  // ファイルパスを設定

    if (!Storage::exists("public/uploads/{$file}")) {
        abort(404);
    }

    return Response::download($path);
});



// Laravelのコントローラーを経由して提供
Route::get('Project_name/downloadpdf/{file}', function ($file) {
    $path = public_path('storage/uploads/' . $file);

    Log::info('Request to download file', ['file' => $file]);
    Log::info('Request to download path', ['path' => $path]);

    if (!file_exists($path)) {
        Log::info('404 - File not found: ' . $file);
        return response()->json(['error' => 'File not found'], 404);
    }

    Log::info('Serving file: ' . $file);

    // ファイルをダウンロードレスポンスとして返却
    return response()->download($path, $file, [
        'Content-Type' => mime_content_type($path),
    ]);
})->middleware('cors');



// Route::get('/storage/uploads/{file}', function ($file) {
//     $path = storage_path('storage/uploads/' . $file);

//     if (!file_exists($path)) {
//         return response()->json(['error' => 'File not found.'], 404)
//             ->header('Access-Control-Allow-Origin', '*');
//     }

//     return response()->stream(function () use ($path) {
//         $stream = fopen($path, 'rb');
//         fpassthru($stream);
//         fclose($stream);
//     }, 200, [
//         'Content-Type' => mime_content_type($path),
//         'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
//         'Access-Control-Allow-Origin' => '*',
//     ]);
// });

// routes/web.php

// Route::get('/storage/uploads/{filename}', function ($filename) {
//     return response()->file(storage_path('/storage/uploads/' . $filename));
// })->middleware('cors');


// // 'cors' ミドルウェアを適用
// Route::middleware(['cors'])->group(function () {
//     Route::get('/example', [ExampleController::class, 'index']);
// });

// Route::middleware('cors')->get('/example', [MyController::class, 'index']);

// Route::get('/storage/{fileName}', [FileController::class, 'download'])->name('file.download');

// Route::options('/{any}', function () {
//     return response()->json([], 204);
// })->where('any', '.*');

// routes/api.php
// Route::post('/Project_name', function (Request $request) {
//     //var_dump($request->all());
//     // var_dump($request->file());
//     // リクエスト内容を確認 
//     Log::info('リクエスト全体:', $request->all()); // Log クラスを使用してリクエストデータを記録
//     Log::info('アップロードファイル:', $request->file()); // Log クラスを使用してファイルデータを記録
//     return response()->json(['message' => 'リクエストを受信しました！api.php']);
// });

//ストレージにファイルを保存
// Route::post('Project_name', function (Request $request) {
//     try {
//         //Log::debug('全リクエストストレージ:', $request->all());
//         //Log::debug('アップロードファイルストレージ:', $request->file('file'));

//         // バリデーション
//         $request->validate([
//             'user_id' =>'required|file|mimes:jpg,png,pdf|max:2048', // 最大2MBのjpg, png, pdfのみ
//             'project_name' => 'required|file|mimes:jpg,png,pdf|max:2048',
//             'finishing_table_name' => 'required|file|mimes:jpg,png,pdf|max:2048',
//             'floor_plan_name' => 'required|file|mimes:jpg,png,pdf|max:2048',
//             'machinery_equipment_diagram_all_name' => 'required|file|mimes:jpg,png,pdf|max:2048',
//             'bim_drawing_name' => 'required|file|mimes:jpg,png,pdf|max:2048',
//             'meeting_log_name' => 'required|file|mimes:jpg,png,pdf|max:2048',
//         ]);

//         // 各ファイルを処理
//         $files = [
//             'user_id',
//             'project_name',
//             'finishing_table_name',
//             'floor_plan_name',
//             'machinery_equipment_diagram_all_name',
//             'bim_drawing_name',
//             'meeting_log_name'
//         ];

//         $filePaths = [];

//         foreach ($files as $fileKey) {
//             $file = $request->file($fileKey);
//             if ($file) {
//                 // 'uploads'ディレクトリにファイルを保存
//                 $filePath = $file->store('uploads');
//                 $filePaths[$fileKey] = $filePath; // 各ファイルのパスを保存
//                // Log::info("ファイルが保存されましたストレージ: $filePath");
//                 //Log::info("ファイルの保存先: " . storage_path('app/' . $filePath));
//             } else {
//                 return response()->json(['error' => "$fileKey はファイルがアップロードされていません"], 400);
//             }
//         }

//         // 保存したファイルのパスを返す
//         return response()->json([
//             'message' => 'ファイルが保存されましたパス',
//             'filePaths' => $filePaths
//         ]);
//     } catch (\Exception $e) {
//         Log::error("エラー: " . $e->getMessage());
//         return response()->json(['error' => 'ファイルの処理中にエラーが発生しました。パス'], 500);
//     }
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
