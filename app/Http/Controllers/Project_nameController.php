<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\project_name;
use Illuminate\Support\Facades\DB;
use App\Models\Drawing;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;



class Project_nameController extends Controller
{

    //uplodad,downloadメソッド
    public function upload(Request $request)
    {
        try {
            Log::info('情報メッセージupload: 変数の値は', ['変数名' => $request]);
            //バリデーション
            $validatedData = $request->validate([
                'id' => 'nullable|max:2048',
                'project_name' => 'required|file|mimes:jpg,png,pdf|max:204800', // 最大200MB
                'finishing_table_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'floor_plan_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'machinery_equipment_diagram_all_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'bim_drawing_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'meeting_log_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
            ]);
            Log::info('情報メッセージupload: 変数の値は', ['変数名' => $validatedData]);
            // ファイルの保存
            $filePaths = [];
            $fileFields = [
                'id',
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
                    $existingFilePath = storage_path('app/public/uploads/' . $originalFileName);
                    if (file_exists($existingFilePath)) {
                        // 既存のファイルを削除
                        unlink($existingFilePath);
                    }

                    // 'uploads'ディレクトリにファイルを保存（上書き）
                    $filePath = $file->storeAs('uploads', $originalFileName);
                    $filePaths[$fileKey] = $filePath; // 各ファイルのパスを保存
                    $filePaths[$fileKey . '_file'] = $originalFileName; 
                    // ファイルパスとファイル名を個別に設定
                    // $filePaths[$fileKey] = [
                    //     'path' => $filePath, // ファイルの保存パス
                    //     'name' => $originalFileName, // ファイル名
                    // ];

        
                    Log::info("ファイルが保存されました: $filePath");
                    // サムネイル用のディレクトリパス
                    $thumbnailDirectory = storage_path('app/public/thumbnails/');
                    if (!file_exists($thumbnailDirectory)) {
                        mkdir($thumbnailDirectory, 0755, true); // ディレクトリが存在しない場合は作成
                    }
                    // PDFの場合にJPEGサムネイルを生成
                    $extension = $file->getClientOriginalExtension();
                    if (strtolower($extension) === 'pdf') {
                        // JPEGサムネイルの保存パス
                        $baseFileName = pathinfo($originalFileName, PATHINFO_FILENAME); // 元のファイル名から拡張子を除去
                        $thumbnailPath = $thumbnailDirectory . $baseFileName . '.jpg'; // .jpeg の場合も考慮して .jpg で統一

                        // PDFからJPEGへの変換（pdftoppmを使用）
                        $pdfPath = escapeshellarg(storage_path('app/public/uploads/' . $originalFileName));
                        $thumbnailBasePath = escapeshellarg($thumbnailDirectory . $baseFileName);
                        $command = "pdftoppm -jpeg -f 1 -singlefile $pdfPath $thumbnailBasePath";

                        // コマンドの実行
                        $output = shell_exec($command);

                        // サムネイル生成結果を確認
                        if (file_exists($thumbnailPath)) {
                            Log::info("サムネイルが生成されました: $thumbnailPath");
                            $filePaths[ $fileKey . '_thumbnail' ] = 'thumbnails/' . basename($thumbnailPath); // サムネイルのパスを保存
                        } else {
                            Log::error("サムネイルの生成に失敗しました: $command | 出力: $output");
                        }
                    }
                } else {
                    // ファイルが無効または空の場合はスキップ
                    Log::info("ファイルが空または無効ですupdate: $fileKey");
                }
            }

            DB::transaction(function () use ($filePaths, $validatedData) {
                try {
                    // 認証ユーザーのIDを取得
                    $user_id = auth()->id() ?? 1;

                    // プロジェクトデータの取得または作成・更新
                    $project_name = project_name::updateOrCreate(
                        ['id' => $validatedData['id'] ?? null],
                        [
                            'user_id' => $user_id,
                            'project_name' => $filePaths['project_name'] ?? null,
                        ]
                    );

                    // drawingデータの取得または作成
                    $drawing = $project_name->drawing()->firstOrCreate(
                        ['project_name_id' => $project_name->id]
                    );

                    Log::info('filePaths 配列の内容', $filePaths);

                    // design_drawingデータの取得または更新
                    if (!empty($filePaths['finishing_table_name_file'])) {
                        $drawing->design_drawing()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['finishing_table_name' => $filePaths['finishing_table_name_file']]
                        );
                    }
                    $thumbnailKey = 'finishing_table_name_thumbnail';  // 'finishing_table_name' に対応するサムネイルキー
                    if (!empty($filePaths[$thumbnailKey])) {
                       // Log::info("サムネイルパスが見つかりました: " . $filePaths[$thumbnailKey]);
                        $drawing->design_drawing()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['finishing_table_view_path' => $filePaths[$thumbnailKey]]
                        );
                    } else {
                        Log::warning("サムネイルパスが見つかりません: $thumbnailKey");
                    }

                    if (!empty($filePaths['finishing_table_name'])) {
                        $drawing->design_drawing()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['finishing_table_pdf_path' => $filePaths['finishing_table_name']]
                        );
                    }


                    // structural_diagramデータの取得または更新
                    if (!empty($filePaths['floor_plan_name_file'])) {
                        $drawing->structural_diagram()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['floor_plan_name' => $filePaths['floor_plan_name_file']]
                        );
                    }
                    if (!empty($filePaths['floor_plan_name_thumbnail'])) {
                        $drawing->structural_diagram()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['floor_plan_view_path' => $filePaths['floor_plan_name_thumbnail']]
                        );
                    }
                    if (!empty($filePaths['floor_plan_name'])) {
                        $drawing->structural_diagram()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['floor_plan_pdf_path' => $filePaths['floor_plan_name']]
                        );
                    }

                    // equipment_diagramデータの取得または更新
                    if (!empty($filePaths['machinery_equipment_diagram_all_name_file'])) {
                        $drawing->equipment_diagram()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['machinery_equipment_diagram_all_name' => $filePaths['machinery_equipment_diagram_all_name_file']]
                        );
                    }
                    if (!empty($filePaths['machinery_equipment_diagram_all_name_thumbnail'])) {
                        $drawing->equipment_diagram()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['machinery_equipment_diagram_all_view_path' => $filePaths['machinery_equipment_diagram_all_name_thumbnail']]
                        );
                    }
                    if (!empty($filePaths['machinery_equipment_diagram_all_name'])) {
                        $drawing->equipment_diagram()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['machinery_equipment_diagram_all_pdf_path' => $filePaths['machinery_equipment_diagram_all_name']]
                        );
                    }

                    // bim_drawingデータの取得または更新
                    if (!empty($filePaths['bim_drawing_name_file'])) {
                        $drawing->bim_drawing()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['bim_drawing_name' => $filePaths['bim_drawing_name_file']]
                        );
                    }
                    if (!empty($filePaths['bim_drawing_name_thumbnail'])) {
                        $drawing->bim_drawing()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['bim_drawing_view_path' => $filePaths['bim_drawing_name_thumbnail']]
                        );
                    }
                    Log::info('更新または作成されるデータ', [
                        '条件' => ['drawing_id' => $drawing->id],
                        'データ' => ['bim_drawing_pdf_path' => $filePaths['bim_drawing_name']]
                    ]);

                    if (!empty($filePaths['bim_drawing_name'])) {
                        $drawing->bim_drawing()->updateOrCreate(
                            ['drawing_id' => $drawing->id],
                            ['bim_drawing_pdf_path' => $filePaths['bim_drawing_name']]
                        );
                    }

                    // meeting_logデータの取得または更新
                    if (!empty($filePaths['meeting_log_name_file'])) {
                        $project_name->meeting_log()->updateOrCreate(
                            ['project_id' => $project_name->id],
                            ['meeting_log_name' => $filePaths['meeting_log_name_file']]
                        );
                    }
                    if (!empty($filePaths['meeting_log_name_thumbnail'])) {
                        $project_name->meeting_log()->updateOrCreate(
                            ['project_id' => $project_name->id],
                            ['meeting_log_view_path' => $filePaths['meeting_log_name_thumbnail']]
                        );
                    }
                    if (!empty($filePaths['meeting_log_name'])) {
                        $project_name->meeting_log()->updateOrCreate(
                            ['project_id' => $project_name->id],
                            ['meeting_log_pdf_path' => $filePaths['meeting_log_name']]
                        );
                    }

                    // 成功時のレスポンス
                    return response()->json(
                        [
                            'message' => 'ファイルパスが保存されました！upload',
                            'file_paths' => $filePaths,
                        ],
                        201
                    );
                } catch (\Exception $e) {
                    // エラーログとレスポンス
                    Log::error("エラーupload: " . $e->getMessage());
                    return response()->json(['error' => 'ファイルの処理中にエラーが発生しました。upload'], 500);
                }
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

    //ダウンロードdownloagメソッド
    public function download($id)
    {
        //  Log::info('情報メッセージextraction: 変数の値は', ['変数名' => $id]);

        // `$id` に基づいて特定のプロジェクトを取得
        $project = project_name::with([
            'drawing.design_drawing',
            'drawing.structural_diagram',
            'drawing.equipment_diagram',
            'drawing.bim_drawing',
            'meeting_log',
        ])->findOrFail($id);


        $filePath = storage_path('app/public/' . $project->project_name); // 適切なフィールドを指定
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath);
    }




    //検索searchメソッド
    public function search(Request $request)
    {
        $query = $request->input('query'); //検索クエリの取得
        //部分一致検索を実行
        $project_name = project_name::where('project_name', 'like', '%' . $query . '%')->get();
        //Log::info('情報メッセージsearch: 変数の値は', ['変数名' => $project_name]);
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
        //Log::info('情報メッセージselect: 変数の値は', ['変数名' => $selectedProjectIds]);
        // プロジェクトIDが取得できなかった場合の処理を追加
        if (empty($selectedProjectIds) || !is_array($selectedProjectIds)) {
            Log::error('プロジェクトIDが空または無効です:', ['リクエストデータ' => $request->all()]);
            return response()->json(['error select' => 'プロジェクトIDが選択されていません'], 400);
        }

        $project_name = project_name::whereIn('id', $selectedProjectIds)->with(['drawing.design_drawing', 'drawing.structural_diagram', 'drawing.equipment_diagram', 'drawing.bim_drawing', 'meeting_log'])->get();
        Log::info('情報メッセージselect: 変数の値は', ['変数名' => $project_name]);
        // return view('project_name.select', ['project_name' => $selectedProjects]);
        //return view('project_name.select', compact('project_name'));
        //return response()->json($project_name); // JSON形式で結果を返す
        return response()->json(['redirect' => 'Project_name/select', 'project_name' => $project_name]); // JSON形式で結果を返しリダイレクト
    }

    public function show($id)
    {
        Log::info('情報メッセージshow: 変数の値は', ['変数名' => $id]);

        // `$id` に基づいて特定のプロジェクトを取得
        $project_name = project_name::with([
            'drawing.design_drawing',
            'drawing.structural_diagram',
            'drawing.equipment_diagram',
            'drawing.bim_drawing',
            'meeting_log',
        ])->findOrFail($id);
        Log::info('情報メッセージshow: 変数の値は', ['変数名' => $project_name]);
        // プロジェクト詳細情報をJSONで返却
        return response()->json([
            'redirect' => 'Project_name/show',
            'project_name' => $project_name
        ]); // JSON形式で結果を返しリダイレクト
    }

      //抽出extraction downloadへviewパスからjpgのURIを返す
    public function extraction($id)
    {
        // '%_view_path' のカラム名を持つデータをフィルタリングする関数
        $filterViewPath = function ($items) {
            Log::info('フィルタリング開始items:', ['items' => $items]); // 渡されるitemsを確認

            return collect($items)->filter(function ($value, $key) { // keyとvalue両方を取得
                Log::info('フィルタリング中のitem:', ['key' => $key, 'value' => $value]);

                // '_view_path' または 'name' で終わるキーをチェック
                if (is_string($key) && (substr($key, -10) === '_view_path' || substr($key, -5) === '_name')) {
                    Log::info('該当するキーを発見:', ['key' => $key, 'value' => $value]);
                    return true; // フィルタリング対象のアイテムを保持
                }

                return false; // '_view_path' で終わらない場合は除外
            })->toArray(); // コレクションを配列に変換
        };

        Log::info('フィルタリング関数準備完了');

        // プロジェクトデータを取得
        Log::info('プロジェクトデータ取得前id:', ['id' => $id]);
        $project = project_name::with([
            'drawing.design_drawing',
            'drawing.structural_diagram',
            'drawing.equipment_diagram',
            'drawing.bim_drawing',
            'meeting_log',
        ])->findOrFail($id);

        Log::info('プロジェクトデータ取得後project:', ['project' => $project]);

        // 各リレーションに対してフィルタリングを適用し、URLを追加
        $filteredData = [
            'design_drawing' => $filterViewPath($project->drawing->design_drawing ?? []),
            'structural_diagram' => $filterViewPath($project->drawing->structural_diagram ?? []),
            'equipment_diagram' => $filterViewPath($project->drawing->equipment_diagram ?? []),
            'bim_drawing' => $filterViewPath($project->drawing->bim_drawing ?? []),
        ];

  // public/thumbnails/）に基づいてURLを変換
foreach ($filteredData as $key => $items) {
    foreach ($items as $itemKey => $itemValue) {
        // パス情報のみをURLに変換
        if (strpos($itemValue, 'thumbnails/') !== false) {
            // サーバー上のURLを動的に生成
            $filteredData[$key][$itemKey] = url('storage/' . str_replace('public/', '', $itemValue)); // URL変換
        } else {
            // パス情報がURLでない場合も修正
            $filteredData[$key][$itemKey] = $itemValue;
        }

        // バックスラッシュ（￥）をスラッシュに変換
        $filteredData[$key][$itemKey] = str_replace('\\', '/', $filteredData[$key][$itemKey]);

        // 最後のバックスラッシュが残っている場合を削除
        $filteredData[$key][$itemKey] = rtrim($filteredData[$key][$itemKey], '\\');
    }
}



        // フィルタリング後のデータをJSON形式でログに出力 (エスケープを防ぐ)
        $jsonData = json_encode($filteredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // バックスラッシュが残っている場合、それを取り除く
        $jsonData = stripslashes($jsonData);

        // 修正したデータをログに出力
        //Log::info('フィルタリング後のデータ (JSON):', ['filteredData' => $jsonData]);
        // フィルタリング後のデータをそのまま配列としてログに出力
        Log::info('フィルタリング後のデータfilteredData:', ['filteredData' => $filteredData]);
        // バックスラッシュが含まれていないか確認
        //Log::info('フィルタリング後の生データ:', ['filteredData' => print_r($filteredData, true)]);

        // 変換後のデータを格納する配列
        $converted_projects = [];

        // 変換処理
        foreach ($filteredData as $key => $value) {
            foreach ($value as $sub_key => $sub_value) {
                if (str_contains($sub_key, "name")) { // "name"が含まれるキーの場合
                    $file_name = $sub_value;
                    // 対応する "view_path" を取得
                    $view_path_key = str_replace("name", "view_path", $sub_key);
                    if (array_key_exists($view_path_key, $value)) {
                        // 新しいキーを作成
                        $new_key = "{$key}.{$file_name}";
                        $new_value = $value[$view_path_key];
                        // 新しいキーと値を配列に追加
                        $converted_projects[$new_key] = $new_value;
                    }
                }
            }
        }
        Log::info('フィルタリング後のデータ$converted_projects:', ['converted_projects' => $converted_projects]);


        // フィルタリング後のデータをJSON形式でログに出力 (エスケープを防ぐ)
        // Log::info('フィルタリング後のデータ (JSON):', [
        //     'filteredData' => json_encode($filteredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        // ]);

        // フィルタリングされたデータをレスポンスとして返却
        return response()->json([
            'redirect' => 'Project_name/download',
            'filteredData' => $converted_projects, // '_view_path' のみ抽出されたデータ（URL付き）
        ]);
    }



    //部分変更 updateメソッド(編集)
    // public function update(Request $request, $id)
    // {
    //     // dd($id);
    //     //$id = $request->request('id');
    //     //バリデーション
    //     $validatedData = $request->validate([
    //         'project_name' => 'nullable|string|max:255',
    //         'finishing_table_name' => 'nullable|string|max:255',
    //         'floor_plan_name' => 'nullable|string|max:255',
    //         'machinery_equipment_diagram_all_name' => 'nullable|string|max:255',
    //         'bim_drawing_name' => 'nullable|string|max:255',
    //         'meeting_log_name' => 'nullable|string|max:255',
    //     ]);

    //     //dd($id);
    //     $project_name = project_name::findOrFail($id);

    //     // トランザクション処理で一括保存
    //     DB::transaction(function () use ($validatedData, $project_name) {
    //         //$user_id = auth()->id(); // 認証ユーザーのIDを取得
    //         // プロジェクトデータを更新
    //         $project_name->update([
    //             'user_id' =>  $validatedData['user_id'] ?? $project_name->user_id,
    //             'project_name' => $validatedData['project_name'] ?? $project_name->project_name,
    //         ]);
    //         // drawingデータを保存（プロジェクトとリレーション）
    //         $drawing = $project_name->drawing()->first();
    //         $drawing->update([
    //             'project_name_id' => $project_name->id,
    //         ]);
    //         // design_drawingデータを保存（プロジェクトとリレーション）
    //         $project_name->drawing()->first()->design_drawing()->update([
    //             'drawing_id' => $drawing->id, //主キーと外部キーを連携
    //             'finishing_table_name' => $validatedData['finishing_table_name'] ?? null,
    //         ]);
    //         // structual_diagramデータを保存（プロジェクトとリレーション）
    //         $project_name->drawing()->first()->structural_diagram()->update([
    //             'drawing_id' => $drawing->id, //主キーと外部キーを連携
    //             'floor_plan_name' => $validatedData['floor_plan_name'] ?? null,
    //         ]);
    //         // equipment_diagramデータを保存（プロジェクトとリレーション）
    //         $project_name->drawing()->first()->equipment_diagram()->update([
    //             'drawing_id' => $drawing->id, //主キーと外部キーを連携
    //             'machinery_equipment_diagram_all_name' => $validatedData['machinery_equipment_diagram_all_name'] ?? null,
    //         ]);
    //         // bim_drawingデータを保存（プロジェクトとリレーション）
    //         $project_name->drawing()->first()->bim_drawing()->update([
    //             'drawing_id' => $drawing->id, //主キーと外部キーを連携
    //             'bim_drawing_name' => $validatedData['bim_drawing_name'] ?? null,
    //         ]);
    //         // meeting_logデータを保存（プロジェクトとリレーション）
    //         $project_name->meeting_log()->update([
    //             'project_id' => $project_name->id, //主キーと外部キーを連携
    //             'meeting_log_name' => $validatedData['meeting_log_name'] ?? null,
    //         ]);
    //     });
    //     return response()->json(['message' => '図面と書類が作成されました！'], 201);
    // }

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
        $project_name = project_name::with([
            'drawing.design_drawing',
            'drawing.structural_diagram',
            'drawing.equipment_diagram',
            'drawing.bim_drawing',
            'meeting_log',
        ])->get();
        //Log::info('情報メッセージindex:', ['変数名' => $posts]);
        // デバッグメッセージを出力
        // if ($posts->isEmpty()) {
        //     dd("データが存在しません。"); // Laravelの dd() でデバッグ終了
        // }
        // return view('project_name.index', compact('posts'));
        return response()->json(['project_name' => $project_name]); // JSON形式で結果を返しリダイレクト
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
