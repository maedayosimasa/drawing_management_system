<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\project_name;
use Illuminate\Support\Facades\DB;
use App\Models\Drawing;

class Project_nameController extends Controller
{

    //一覧画面のテーブルすべてを取得
    public function index()
    {
        $posts = Project_name::all();
        // デバッグでデータを確認
        // dd($posts);
        // // デバッグメッセージを出力
        // if ($posts->isEmpty()) {
        //     dd("データが存在しません。"); // Laravelの dd() でデバッグ終了
        // }
        return view('project_name.index', compact('posts'));
    }

    // データ保存を行うメソッド
    public function store(Request $request)
    {
        // トランザクション処理で一括保存
        DB::transaction(function () use ($request) {
            //$user_id = auth()->id(); // 認証ユーザーのIDを取得
            // プロジェクトデータを保存
            $project_name = project_name::create([
                'user_id' =>  $request->input('user_id'),
                'project_name' => $request->input('project_name', 'デフォルトプロジェクト名'),
            ]);
            // drawingデータを保存（プロジェクトとリレーション）
            $drawing = $project_name->drawing()->create([
                'project_name_id' => $project_name->id
            ]);
            // design_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->design_drawing()->create([
                'drawing_id' => $drawing->id,
                'finishing_table_name' => $request->input('finishing_table_name', null),
            ]);
            // structual_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing() ->first()->structural_diagram()->create([
                'drawing_id' => $drawing->id,
                'floor_plan_name' => $request->input('floor_plan_name', null),
                ]);
            // equipment_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->equipment_diagram()->create([
                'drawing_id' => $drawing->id,
                'machinery_equipment_diagram_all_name' => $request->input('machinery_equipment_diagram_all_name', null),
            ]);
            // bim_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->bim_drawing()->create([
                'drawing_id' => $drawing->id,
                'bim_drawing_name' => $request->input('bim_drawing_name', null),
            ]);
            // meeting_logデータを保存（プロジェクトとリレーション）
            $project_name->meeting_log()->create([
                'project_id' => $project_name->id,
                'meeting_log_name' => $request->input('meeting_log_name', null),
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

