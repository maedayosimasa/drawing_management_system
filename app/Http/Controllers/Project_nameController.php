<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\project_name;
use Illuminate\Support\Facades\DB;
use App\Models\Drawing;


class Project_nameController extends Controller
{

    //検索searchメソッド
    public function search(Request $request) {
        $query = $request->input('query'); //検索クエリの取得
        //部分一致検索を実行
        $project_name = project_name::where('project_name', 'like', '%'. $query . '%')->get();
        //検索ビューに渡す
        return view('project_name.searct_results', compact('project_name' , 'query'));
    }

    //プロジェクト詳細を表示するshowメソッド
    public function show($id=4){
        $project_name = project_name::findOrFail($id);
        return view('project_name.show', compact('project_name'));
    }

    //部分変更 updateメソッド(編集)
    public function update(Request $request, $id)
    {
        //バリデーション
        $validatedData = $request->validate([
            'project_name' => 'nullable|string|max:255',
            'finishing_table_name' => 'nullable|string|max:255',
            'floor_plan_name' => 'nullable|string|max:255',
            'machinery_equipment_diagram_all_name' => 'nullable|string|max:255',
            'bim_drawing_name' => 'nullable|string|max:255',
            'meeting_log_name' => 'nullable|string|max:255',
        ]);

        $project_name = project_name::findOrFail($id=4);

            // トランザクション処理で一括保存
        DB::transaction(function () use ($validatedData, $project_name) {
            //$user_id = auth()->id(); // 認証ユーザーのIDを取得
            // プロジェクトデータを更新
            $project_name ->update([
                'user_id' =>  $validatedData['user_id']?? $project_name->user_id,
                'project_name' => $validatedData['project_name']?? $project_name->project_name,
            ]);
            // drawingデータを保存（プロジェクトとリレーション）
            $drawing = $project_name->drawing()->first();
            $drawing->update([
                'project_name_id' => $project_name->id,
            ]);
            // design_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->design_drawing()->update([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'finishing_table_name' => $validatedData['finishing_table_name']?? null,
            ]);
            // structual_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->structural_diagram()->update([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'floor_plan_name' => $validatedData['floor_plan_name'] ?? null,
            ]);
            // equipment_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->equipment_diagram()->update([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'machinery_equipment_diagram_all_name' => $validatedData['machinery_equipment_diagram_all_name'] ?? null,
            ]);
            // bim_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->bim_drawing()->update([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'bim_drawing_name' => $validatedData['bim_drawing_name'] ?? null,
            ]);
            // meeting_logデータを保存（プロジェクトとリレーション）
            $project_name->meeting_log()->update([
                'project_id' => $project_name->id,//主キーと外部キーを連携
                'meeting_log_name' => $validatedData['meeting_log_name'] ?? null,
            ]);
        });
        // 保存後のリダイレクト
        return redirect()->route('project_name.show')->with('success', '図面と書類が更新されました！');
    
        }

    // 一括削除処理
    // public function delete(Request $request)
    // {
    //     //$project_name = project_name::findOrFail($id = 4);
    //     // チェックされたプロジェクトIDを取得
    //     $project_name = $request->input('project_name_id');

    //     // プロジェクトIDが指定されていれば削除
    //     if ($project_name) {
    //         project_name::whereIn('id', $project_name)->delete();
    //         return redirect()->route('project_name.index')->with('success', '選択したプロジェクトを削除しました');
    //     }

    //     return redirect()->route('project_name.index')->with('error', '削除するプロジェクトが選択されていません');
    // }   
    
    //一覧画面のテーブルすべてを取得
    public function index()
    {
        $posts = Project_name::all();
        // デバッグでデータを確認
        // dd($posts);
        // デバッグメッセージを出力
        // if ($posts->isEmpty()) {
        //     dd("データが存在しません。"); // Laravelの dd() でデバッグ終了
        // }
        return view('project_name.index', compact('posts'));
    }

    // データ保存を行うメソッド
    public function store(Request $request)
    {

        //バリデーションルール定義
        $validatedDate = $request->validate([
            'user_id' => 'required|integer|max:11',
            'project_name' => 'nullable|string|max:255',
            'finishing_table_name' => 'nullable|string|max:255',
            'floor_plan_name' => 'nullable|string|max:255',
            'machinery_equipment_diagram_all_name' => 'nullable|string|max:255',
            'bim_drawing_name' => 'nullable|string|max:255',
            'meeting_log_name' => 'nullable|string|max:255',
        ]);


        // トランザクション処理で一括保存
        DB::transaction(function () use ($validatedDate) {
            //$user_id = auth()->id(); // 認証ユーザーのIDを取得
            // プロジェクトデータを保存
            $project_name = project_name::create([
                'user_id' =>  $validatedDate['user_id'],
                'project_name' => $validatedDate['project_name'], 'デフォルトプロジェクト名',
            ]);
            // drawingデータを保存（プロジェクトとリレーション）
            $drawing = $project_name->drawing()->create([
                'project_name_id' => $project_name->id
            ]);
            // design_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->design_drawing()->create([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'finishing_table_name' => $validatedDate['finishing_table_name']?? null,
            ]);
            // structual_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->structural_diagram()->create([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'floor_plan_name' => $validatedDate['floor_plan_name'] ?? null,
            ]);
            // equipment_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->equipment_diagram()->create([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'machinery_equipment_diagram_all_name' => $validatedDate['machinery_equipment_diagram_all_name'] ?? null,
            ]);
            // bim_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->bim_drawing()->create([
                'drawing_id' => $drawing->id,//主キーと外部キーを連携
                'bim_drawing_name' =>
                $validatedDate['bim_drawing_name'] ?? null,
            ]);
            // meeting_logデータを保存（プロジェクトとリレーション）
            $project_name->meeting_log()->create([
                'project_id' => $project_name->id,//主キーと外部キーを連携
                'meeting_log_name' =>
                $validatedDate['meeting_log_name'] ?? null,
            ]);
        });
        // 保存後のリダイレクト
        return redirect()->route('project_name.create')->with('success', '図面と書類が作成されました！');
    }



    public function create()
    {
        return view('project_name.create');
    }

    // public function store(Request $request)
    // {
    //     // デバッグ: リクエストデータを確認
    //     // dd($request->all());

    //     $post = Project_name::create([

    //         'user_id' =>  $request->user_id,
    //         'project_name' => $request->project_name,
    //     ]);

    //     $validated['user_id'] = auth()->id();
    //     return back();
    // }
}
