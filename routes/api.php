<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\project_name;
use App\Http\Controllers\Project_nameController;
use Illuminate\Support\Facades\Log; // Logクラスをインポート
use Illuminate\Support\Facades\Storage;


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
Route::get('/project_names', function () {
    $project_names = project_name::with(
        // 必要なリレーションを含むプロジェクト名を取得
        'drawing.design_drawing',
        'drawing.structural_diagram',
        'drawing.equipment_diagram',
        'drawing.bim_drawing',
        'meeting_log'
    )->get();
    return response()->json($project_names); // JSONでデータを返す
});

//RactとのAPIのルート設定
Route::get('Project_name', [Project_nameController::class, 'index']);
Route::post('Project_name', [Project_nameController::class, 'store']);

//upLoad,downLoadルート設定
Route::post('Project_name', [Project_nameController::class, 'upload'])->name('upload');
Route::get('Project_name/download/{id}', [Project_nameController::class, 'download'])->name('download');

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
