<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\project_name;
use App\Http\Controllers\Project_nameController;

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
Route::get('/project_names', function() {
    $project_names = project_name::with(
         // 必要なリレーションを含むプロジェクト名を取得
        'drawing.design_drawing',
        'drawing.structural_diagram',
        'drawing.equipment_diagram',
        'drawing.bim_drawing',
        'meeting_log'
    )->get();
    return response()->json($project_names);// JSONでデータを返す
});

Route::get('Project_name', [Project_nameController::class, 'index']);
Route::post('Project_name', [Project_nameController::class, 'store']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
