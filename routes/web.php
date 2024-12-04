<?php

use App\Http\Controllers\Bim_drawingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Project_nameController;
use App\Http\Controllers\DrawingController;
use App\Http\Controllers\Design_drawingController;
use App\Http\Controllers\Meeting_logController;
use App\Http\Controllers\Equipment_diagramController;
use App\Http\Controllers\Structural_diagramController;
use App\Models\project_name;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//Laravelのルート定義で、/api/project_names にGETリクエストが来たときに、project_name モデルの関連データを取得し、その結果をJSON形式で返す処理
//JSON APIを構築し、プロジェクト一覧データを返す
Route::get('/api/project_names',function() {
    $project_names = project_name::with('drawing.design_drawing', 'drawing.structural_diagram', 'drawing.equipment_diagram', 'drawing.bim_drawing', 'meeting_log')->get();
    return response()->json($project_names);//返すデータをJSON形式に変換
});

//seachのルート設定
Route::get('Project_name/search', [Project_nameController::class, 'search'])->name('project_name.search');


//一覧画面のルート追加
Route::get('project_name', [Project_nameController::class, 'index']);
Route::get('Drawing', [DrawingController::class, 'index']);

//showルート設定
Route::get('Project_name/show/{id?}', [Project_nameController::class, 'show'])->name('project_name.show');
//PUTルート設定
Route::put('project_name/{id}', [Project_nameController::class, 'update'])->name('project_name.update');
//Route::resource() を使用することで、Laravelは次のような標準的なルートを自動的に生成します（コントローラ内で対応するメソッドを実装する必要があります）
//Route::resource('project_name', Project_nameController::class);

// 一括削除のルート
// Route::delete('project_name/show/{id?}', [Project_nameController::class, 'delete'])->name('project_name.show');


//project_name  入力route
Route::get('Project_name/create', [Project_nameController::class, 'create'])->name('project_name.create');
Route::post('Project_name', [Project_nameController::class, 'store'])->name('project_name.store');

//drawing 入力route
Route::get('Drawing/create', [DrawingController::class, 'create']);
Route::post('Drawing', [DrawingController::class, 'store'])->name('drawing.store');

//design_drawing 入力route
Route::get('Design_drawing/create', [Design_drawingController::class, 'create']);
Route::post('Design_drawing', [Design_drawingController::class, 'store'])->name('design_drawing.store');

//meeting_log 入力 route
Route::get('Meeting_log/create', [Meeting_logController::class, 'create']);
Route::post('Meeting_log', [Meeting_logController::class, 'store'])->name('meeting_log.store');

//equipment_diagram 入力 route
Route::get('Equipment_diagram/create', [Equipment_diagramController::class, 'create']);
Route::post('Equipment_diagram', [Equipment_diagramController::class, 'store'])->name('equipment_diagram.store');

//bim_drawing 入力  route
Route::get('Bim_drawing/create', [Bim_drawingController::class, 'create']);
Route::post('Bim_drawing', [Bim_drawingController::class, 'store'])->name('bim_drawing.store');

//structual_diagram 入力 route
Route::get('Structural_diagram/create', [Structural_diagramController::class, 'create']);
Route::post('Structural_diagram', [Structural_diagramController::class, 'store'])->name('structural_diagram.store');




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
