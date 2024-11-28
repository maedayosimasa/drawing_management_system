<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\project_name;

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
    

    public function create() {
        return view('project_name.create');
    }

    public function store(Request $request) {
        // デバッグ: リクエストデータを確認
        // dd($request->all());

        $post = Project_name::create([
           
            'user_id' =>  $request->user_id,
            'project_name' => $request->project_name,
        ]);

        $validated['user_id'] = auth()->id();
        return back();
    }


}
