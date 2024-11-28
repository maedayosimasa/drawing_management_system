<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\project_name;
use Illuminate\Support\Facades\DB;

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
            // プロジェクトデータを保存
            $purojecr_name = Project_name::create([
                'user_id' =>  $request->input('user_id'),
                'project_name' => $request->input('project_name'),
            ]);
            // drawingデータを保存（プロジェクトとリレーション）
            $purojecr_name->drawings()->create([
                'project_id' => $request->input('project_id'),
            ]);
            // design_drawingデータを保存（プロジェクトとリレーション）
            $purojecr_name->drawings()->first()->design_drawing()->create([
                'drawing_id' => $request->input('drawing_id'),
                'finising_table_name' => $request->input('finising_table_name'),
            ]);
            // structual_diagramデータを保存（プロジェクトとリレーション）
            $purojecr_name->drawings()->first()->structual_diagram()->create([
                'drawing_id' => $request->input('drawing_id'),
                'floor_plan_name' => $request->input('floor_plan_name'),
                ]);
            // equipment_diagramデータを保存（プロジェクトとリレーション）
            $purojecr_name->drawings()->first()->equipment_diagram()->create([
                'drawing_id' => $request->input('drawing_id'),
                'machinery_equipment_diagram_all_name' => $request->input('machinery_equipment_diagram_all_name')
            ]);
            // bim_drawingデータを保存（プロジェクトとリレーション）
            $purojecr_name->drawings()->first()->bim_drawing()->create([
                'drawing_id' => $request->input('drawing_id'),
                'bim_drawing_name' => $request->input('bim_drawing_name'),
            ]);
            // meeting_logデータを保存（プロジェクトとリレーション）
            $purojecr_name->meeting_logs()->create([
                'project_id' => $request->input('project_id'),
                'meeting_log_name' => $request->input('meeting_log_name'),
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

