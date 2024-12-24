<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\project_name;
use Illuminate\Support\Facades\DB;
use App\Models\Drawing;
use App\Models\File;
use Illuminate\Support\Facades\Log;


class Project_nameController extends Controller
{

    //uplodad,downloadメソッド
    public function upload(Request $request)
    {
        try {
            //バリデーション
            $validatedData = $request->validate([
                'project_name' => 'required|file|mimes:jpg,png,pdf|max:204800', // 最大200MB
                'finishing_table_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'floor_plan_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'machinery_equipment_diagram_all_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'bim_drawing_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'meeting_log_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
            ]);

            // ファイルの保存
            $filePaths = [];
            $fileFields = [
                'project_name',
                'finishing_table_name',
                'floor_plan_name',
                'machinery_equipment_diagram_all_name',
                'bim_drawing_name',
                'meeting_log_name',
            ];

            foreach ($fileFields as $fileKey) {
                $file = $request->file($fileKey);

                if ($file && $file->isValid()) { // ファイルが存在し、有効な場合のみ処理
                    // 元のファイル名を取得
                    // 配列内の空部分をスキップ
                    // if (empty($fileKey)) {
                    //     continue;
                    // }
                    $originalFileName = $file->getClientOriginalName();
                    //ファイル名をクリーンアップする処理を追加ディレクトリトラバーサル攻撃回避
                    //$originalFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileFields->getClientOriginalName());

                    // ファイルが既に存在する場合は削除（上書き）
                    $existingFilePath = storage_path('app/uploads/' . $originalFileName);
                    if (file_exists($existingFilePath)) {
                        // 既存のファイルを削除
                        unlink($existingFilePath);
                    }

                    // 'uploads'ディレクトリにファイルを保存（上書き）
                    $filePath = $file->storeAs('uploads', $originalFileName);
                    $filePaths[$fileKey] = $filePath; // 各ファイルのパスを保存
                    //Log::info("ファイルが保存されましたストレージControllerController: $filePath");
                    // サムネイル用のディレクトリパス
                    $thumbnailDirectory = storage_path('app/thumbnails/');
                    if (!file_exists($thumbnailDirectory)) {
                        mkdir($thumbnailDirectory, 0755, true); // ディレクトリが存在しない場合は作成
                    }
                    // PDFの場合にサムネイル（SVG）を生成
                    $extension = $file->getClientOriginalExtension();
                    if (strtolower($extension) === 'pdf') {
                        // SVGサムネイルの保存パス
                        $svgFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '.svg';
                        $thumbnailPath = 'uploads/thumbnails/' . $svgFileName;
                        // $thumbnailFullPath = storage_path('app/' . $thumbnailPath);
                        $thumbnailPath = $thumbnailDirectory . $svgFileName;
                        // PDFからSVGへの変換
                        $command = "pdf2svg " . escapeshellarg(storage_path('app/uploads/' . $originalFileName)) . " " . escapeshellarg($thumbnailPath) . " 1";
                        shell_exec($command);
                        shell_exec($command);

                        // サムネイルのパスも配列に追加
                        $filePaths['thumbnail_' . $fileKey] = $thumbnailPath;
                    }
                    // サムネイル生成結果を確認
                    // if (file_exists($thumbnailPath)) {
                    //     Log::info("SVGサムネイルが生成されました: $thumbnailPath");
                    //     $filePaths['thumbnails'][$fileKey] = 'thumbnails/' . $svgFileName; // サムネイルのパスを保存
                    // } else {
                    //     Log::error("SVGサムネイルの生成に失敗しました: $command");
                    //     return response()->json(['error' => "SVGサムネイルの生成に失敗しました: $fileKey"], 500);
                    // }
                } else {
                    // ファイルが無効または空の場合はスキップ
                    Log::info("ファイルが空または無効ですupdate: $fileKey");
                }
            }

            DB::transaction(function () use ($filePaths) {
                // 認証ユーザーのIDを取得
                $user_id = auth()->id() ?? 1;

                // プロジェクトデータを保存
                $project_name = project_name::create([
                    'user_id' => $user_id,
                    'project_name' => $filePaths['project_name'] ?? null, // プロジェクト名のファイルパス
                ]);

                // drawingデータを保存（プロジェクトとリレーション）
                $drawing = $project_name->drawing()->create([
                    'project_name_id' => $project_name->id,
                ]);

                // design_drawingデータを保存（drawingリレーションとファイルパス）
                $project_name->drawing()->first()->design_drawing()->create([
                    'drawing_id' => $drawing->id,
                    'finishing_table_name' => $filePaths['finishing_table_name'] ?? null, // ファイルパス
                ]);

                // structural_diagramデータを保存（drawingリレーションとファイルパス）
                $project_name->drawing()->first()->structural_diagram()->create([
                    'drawing_id' => $drawing->id,
                    'floor_plan_name' => $filePaths['floor_plan_name'] ?? null, // ファイルパス
                ]);

                // equipment_diagramデータを保存（drawingリレーションとファイルパス）
                $project_name->drawing()->first()->equipment_diagram()->create([
                    'drawing_id' => $drawing->id,
                    'machinery_equipment_diagram_all_name' => $filePaths['machinery_equipment_diagram_all_name'] ?? null, // ファイルパス
                ]);

                // bim_drawingデータを保存（drawingリレーションとファイルパス）
                $project_name->drawing()->first()->bim_drawing()->create([
                    'drawing_id' => $drawing->id,
                    'bim_drawing_name' => $filePaths['bim_drawing_name'] ?? null, // ファイルパス
                ]);

                // meeting_logデータを保存（プロジェクトリレーションとファイルパス）
                $project_name->meeting_log()->create([
                    'project_id' => $project_name->id,
                    'meeting_log_name' => $filePaths['meeting_log_name'] ?? null, // ファイルパス
                ]);
            });

            // 保存後のリダイレクト
            return response()->json(
                [
                    'message' => 'ファイルパスが保存されました！upload',
                    'file_paths' => $filePaths,
                ],
                201
            );
            // ダウンロード用リンクの返却
            // return response()->json([
            //     'message' => 'ファイルが正常にアップロードされましたデータベース',
            //     'file_id' => $file->id,
            //     'download_url' => route('download', ['id' => $file->id]),
            // ]);
        } catch (\Exception $e) {
            Log::error("エラーupload: " . $e->getMessage());
            return response()->json(['error' => 'ファイルの処理中にエラーが発生しました。upload'], 500);
        }
    }
    // public function download($id)
    // {
    //     $file = File::findOrFail($id);

    //     $filePath = storage_path('app/public/' . $file->project_name); // 適切なフィールドを指定
    //     if (!file_exists($filePath)) {
    //         return response()->json(['message' => 'File not found'], 404);
    //     }

    //     return response()->download($filePath);
    // }




    //検索searchメソッド
    public function search(Request $request)
    {
        $query = $request->input('query'); //検索クエリの取得
        // dd($query);

        //部分一致検索を実行
        $project_name = project_name::where('project_name', 'like', '%' . $query . '%')->get();
        //Log::info('情報メッセージsearch: 変数の値は', ['変数名' => $project_name]);

        //  dd($project_name);
        //検索ビューに渡す
        // if (empty($query)) {
        //     return redirect()->back()->with('error', '検索キーワードを入力してください。');
        // }
        //return view('project_name.search', compact('project_name', 'query'));
        return response()->json($project_name); // JSON形式で結果を返す

    }


    //selectメソッド
    public function select(Request $request)
    {
        // フォームから選択されたプロジェクトIDを取得
        $selectedProjectIds = $request->input('id');
        Log::info('情報メッセージselect: 変数の値は', ['変数名' => $selectedProjectIds]);
        // プロジェクトIDが取得できなかった場合の処理を追加
        if (empty($selectedProjectIds) || !is_array($selectedProjectIds)) {
            Log::error('プロジェクトIDが空または無効です:', ['リクエストデータ' => $request->all()]);
            return response()->json(['error select' => 'プロジェクトIDが選択されていません'], 400);
        }

        $project_name = project_name::whereIn('id', $selectedProjectIds)->get();
        // return view('project_name.select', ['project_name' => $selectedProjects]);
        //return view('project_name.select', compact('project_name'));
        //return response()->json($project_name); // JSON形式で結果を返す
        return response()->json(['redirect' => 'Project_name/select', 'project_name' => $project_name]); // JSON形式で結果を返しリダイレクト
    }


    //プロジェクト詳細を表示するshowメソッド
    // public function show(Request $request, $id) {
    //     $id = $request->query('id');
    //     Log::info('情報メッセージshow: 変数の値は', ['変数名' => $id]);
    //     //dd($request->query('id'));  // クエリパラメータの 'id' を取得
    //     $project_name = project_name::findOrFail($id);
    //     // dd($id);
    //     //return view('project_name.show', compact('project_name'));
    //     return response()->json($project_name); 
    // }
    public function show($id)
    {
        Log::info('情報メッセージshow: 変数の値は', ['変数名' => $id]);

        $project_name = Project_name::findOrFail($id);
        Log::info('情報メッセージshow: 変数の値は', ['変数名' => $project_name]);
        // プロジェクト詳細情報をJSONで返却
        return response()->json(['redirect' => 'Project_name/show', 'project_name' => $project_name]); // JSON形式で結果を返しリダイレクト
    }

    //部分変更 updateメソッド(編集)
    public function update(Request $request, $id)
    {
        // dd($id);
        //$id = $request->request('id');
        //バリデーション
        $validatedData = $request->validate([
            'project_name' => 'nullable|string|max:255',
            'finishing_table_name' => 'nullable|string|max:255',
            'floor_plan_name' => 'nullable|string|max:255',
            'machinery_equipment_diagram_all_name' => 'nullable|string|max:255',
            'bim_drawing_name' => 'nullable|string|max:255',
            'meeting_log_name' => 'nullable|string|max:255',
        ]);

        //dd($id);
        $project_name = project_name::findOrFail($id);

        // トランザクション処理で一括保存
        DB::transaction(function () use ($validatedData, $project_name) {
            //$user_id = auth()->id(); // 認証ユーザーのIDを取得
            // プロジェクトデータを更新
            $project_name->update([
                'user_id' =>  $validatedData['user_id'] ?? $project_name->user_id,
                'project_name' => $validatedData['project_name'] ?? $project_name->project_name,
            ]);
            // drawingデータを保存（プロジェクトとリレーション）
            $drawing = $project_name->drawing()->first();
            $drawing->update([
                'project_name_id' => $project_name->id,
            ]);
            // design_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->design_drawing()->update([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'finishing_table_name' => $validatedData['finishing_table_name'] ?? null,
            ]);
            // structual_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->structural_diagram()->update([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'floor_plan_name' => $validatedData['floor_plan_name'] ?? null,
            ]);
            // equipment_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->equipment_diagram()->update([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'machinery_equipment_diagram_all_name' => $validatedData['machinery_equipment_diagram_all_name'] ?? null,
            ]);
            // bim_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->bim_drawing()->update([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'bim_drawing_name' => $validatedData['bim_drawing_name'] ?? null,
            ]);
            // meeting_logデータを保存（プロジェクトとリレーション）
            $project_name->meeting_log()->update([
                'project_id' => $project_name->id, //主キーと外部キーを連携
                'meeting_log_name' => $validatedData['meeting_log_name'] ?? null,
            ]);
        });
        return response()->json(['message' => '図面と書類が作成されました！'], 201);
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
            $user_id = auth()->id(); // 認証ユーザーのIDを取得
            // プロジェクトデータを保存
            $project_name = project_name::create([
                'user_id' =>  $validatedDate['user_id'],
                'project_name' => $validatedDate['project_name'],
                'デフォルトプロジェクト名',
            ]);
            // drawingデータを保存（プロジェクトとリレーション）
            $drawing = $project_name->drawing()->create([
                'project_name_id' => $project_name->id
            ]);
            // design_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->design_drawing()->create([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'finishing_table_name' => $validatedDate['finishing_table_name'] ?? null,
            ]);
            // structual_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->structural_diagram()->create([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'floor_plan_name' => $validatedDate['floor_plan_name'] ?? null,
            ]);
            // equipment_diagramデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->equipment_diagram()->create([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'machinery_equipment_diagram_all_name' => $validatedDate['machinery_equipment_diagram_all_name'] ?? null,
            ]);
            // bim_drawingデータを保存（プロジェクトとリレーション）
            $project_name->drawing()->first()->bim_drawing()->create([
                'drawing_id' => $drawing->id, //主キーと外部キーを連携
                'bim_drawing_name' =>
                $validatedDate['bim_drawing_name'] ?? null,
            ]);
            // meeting_logデータを保存（プロジェクトとリレーション）
            $project_name->meeting_log()->create([
                'project_id' => $project_name->id, //主キーと外部キーを連携
                'meeting_log_name' =>
                $validatedDate['meeting_log_name'] ?? null,
            ]);
        });
        // 保存後のリダイレクト
        // return redirect()->route('project_name.create')->with('success', '図面と書類が作成されました！');
        return response()->json(['message' =>
        '図面と書類が作成されました！', 'project_name' => $validatedDate['project_name'],], 201);
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
