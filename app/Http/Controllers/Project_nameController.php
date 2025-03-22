<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\project_name;
use Illuminate\Support\Facades\DB;
use App\Models\drawing; // Drawingモデルをインポート
use App\Models\meetingLog; // MeetingLogモデルをインポート
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
                'project_name' => 'required|string|max:2048',
                'address' => 'nullable|string|max:2048',
                'client' => 'nullable|string|max:2048',
                'construction_period_start' => 'nullable|date|max:2048',
                'construction_period_end' => 'nullable|date|max:2048',
                'completion_date' => 'nullable|date|max:2048',
                'constract_amount' => 'nullable|string|max:2048',
                'use' => 'nullable|string|max:2048',
                'site_area' => 'nullable|string|max:2048',
                'building_area' => 'nullable|string|max:2048',
                'total_floor_area' => 'nullable|string|max:2048',
                'strural' => 'nullable|string|max:2048',
                'floor_number_underground' => 'nullable|string|max:2048',
                'floor_number_ground' => 'nullable|string|max:2048',

                'finishing_table_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800', // 最大200MB
                'layout_diagram_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'floor_plan_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'elevation_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'sectional_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'design_drawing_all_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',

                'structural_floor_plan_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'structural_elevation_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'structural_sectional_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'structural_frame_diagram_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'structural_diagram_all_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',

                'machinery_equipment_diagram_all_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'electrical_equipment_diagram_all_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',

                'bim_drawing_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',

                'meeting_log_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'delivery_documents_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'bidding_documents_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'archived_photo_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'contract_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
                'management_documents_name' => 'nullable|file|mimes:jpg,png,pdf|max:204800',
            ]);
            Log::info('情報メッセージupload:バリデーション', ['変数名' => $validatedData]);
            // ファイルの保存
            $filePaths = [];
            $fileFields = [
                'id',
                'project_name',
                'finishing_table_name',
                'layout_diagram_name',
                'floor_plan_name',
                'elevation_name',
                'sectional_name',
                'design_drawing_all_name',

                'structural_floor_plan_name',
                'structural_elevation_name',
                'structural_sectional_name',
                'structural_frame_diagram_name',
                'structural_diagram_all_name',

                'machinery_equipment_diagram_all_name',
                'electrical_equipment_diagram_all_name',

                'bim_drawing_name',

                'meeting_log_name',
                'delivery_documents_name',
                'bidding_documents_name',
                'archived_photo_name',
                'contract_name',
                'management_documents_name',
            ];

            foreach ($fileFields as $fileKey) {
                $file = $request->file($fileKey);

                // 空部分や無効なキー（string, data）をスキップ
                if (empty($fileKey) || $fileKey === 'string' || $fileKey === 'data') {
                    continue;
                }

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


                    Log::info("storageにファイルが保存されました: $filePath");
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
                            $filePaths[$fileKey . '_thumbnail'] = 'thumbnails/' . basename($thumbnailPath); // サムネイルのパスを保存
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
                            'project_name' => $validatedData['project_name'] ?? 'デフォルトプロジェクト名',
                            'address' => $validatedData['address'] ?? null,
                            'client' => $validatedData['client'] ?? null,
                            'construction_period_start' => $validatedData['construction_period_start'] ?? null,
                            'construction_period_end' => $validatedData['construction_period_end'] ?? null,
                            'completion_date' => $validatedData['completion_date'] ?? null,
                            'constract_amount' => $validatedData['constract_amount'] ?? null,
                            'use' => $validatedData['use'] ?? null,
                            'site_area' => $validatedData['site_area'] ?? null,
                            'building_area' => $validatedData['building_area'] ?? null,
                            'total_floor_area' => $validatedData['total_floor_area'] ?? null,
                            'strural' => $validatedData['strural'] ?? null,
                            'floor_number_underground' => $validatedData['floor_number_underground'] ?? null,
                            'floor_number_ground' => $validatedData['floor_number_ground'] ?? null,
                        ]
                    );
                    // drawingデータの取得または作成
                    $drawing = $project_name->drawing()->firstOrCreate(['project_name_id' => $project_name->id]);

                    Log::info('filePaths 配列の内容', $filePaths);
                    Log::info('validatedDat 全体の配列の内容', $validatedData);

                    // 必要なキーの確認
                    $requiredKeys = [
                        'finishing_table_name_file',
                        'finishing_table_name_thumbnail',
                        'finishing_table_name',
                        'layout_diagram_name_file',
                        'layout_diagram_name_thumbnail',
                        'layout_diagram_name',
                        'floor_plan_name_file',
                        'floor_plan_name_thumbnail',
                        'floor_plan_name',
                        'elevation_name_file',
                        'elevation_name_thumbnail',
                        'elevation_name',
                        'sectional_name_file',
                        'sectional_name_thumbnail',
                        'sectional_name',
                        'design_drawing_all_name_file',
                        'design_drawing_all_name_thumbnail',
                        'design_drawing_all_name',
                        'structural_floor_plan_name_file',
                        'structural_floor_plan_name_thumbnail',
                        'structural_floor_plan_name',
                        'machinery_equipment_diagram_all_name_file',
                        'machinery_equipment_diagram_all_name_thumbnail',
                        'machinery_equipment_diagram_all_name',
                        'bim_drawing_name_file',
                        'bim_drawing_name_thumbnail',
                        'bim_drawing_name',
                        'meeting_log_name_file',
                        'meeting_log_name_thumbnail',
                        'meeting_log_name',
                    ];

                    // foreach ($requiredKeys as $key) {
                    //     if (!array_key_exists($key, $filePaths)) {
                    //         throw new \Exception("Missing required key in filePaths: $key");
                    //     }
                    // }

                    Log::info('structural_floor_plan_pdf_path の値', ['value' => $filePaths['structural_floor_plan_name'] ?? '未設定']);
                    // design_drawingデータの取得または更新
                    $drawing->design_drawing()->updateOrCreate(
                        ['drawing_id' => $drawing->id],
                        [
                            'finishing_table_name' => $filePaths['finishing_table_name_file'] ?? null,
                            'finishing_table_view_path' => $filePaths['finishing_table_name_thumbnail'] ?? null,
                            'finishing_table_pdf_path' => $filePaths['finishing_table_name'] ?? null,
                            'layout_diagram_name' => $filePaths['layout_diagram_name_file'] ?? null,
                            'layout_diagram_view_path' => $filePaths['layout_diagram_name_thumbnail'] ?? null,
                            'layout_diagram_pdf_path' => $filePaths['layout_diagram_name'] ?? null,
                            'floor_plan_name' => $filePaths['floor_plan_name_file'] ?? null,
                            'floor_plan_view_path' => $filePaths['floor_plan_name_thumbnail'] ?? null,
                            'floor_plan_pdf_path' => $filePaths['floor_plan_name'] ?? null,
                            'elevation_name' => $filePaths['elevation_name_file'] ?? null,
                            'elevation_view_path' => $filePaths['elevation_name_thumbnail'] ?? null,
                            'elevation_pdf_path' => $filePaths['elevation_name'] ?? null,
                            'sectional_name' => $filePaths['sectional_name_file'] ?? null,
                            'sectional_view_path' => $filePaths['sectional_name_thumbnail'] ?? null,
                            'sectional_pdf_path' => $filePaths['sectional_name'] ?? null,
                            'design_drawing_all_name' => $filePaths['design_drawing_all_name_file'] ?? null,
                            'design_drawing_all_view_path' => $filePaths['design_drawing_all_name_thumbnail'] ?? null,
                            'design_drawing_all_pdf_path' => $filePaths['design_drawing_all_name'] ?? null,
                        ]
                    );

                    // structural_diagramデータの取得または更新
                    $result = $drawing->structural_diagram()->updateOrCreate(
                        ['drawing_id' => $drawing->id],
                        [
                            'structural_floor_plan_name' => $filePaths['structural_floor_plan_name_file'] ?? null,
                            'structural_floor_plan_view_path' => $filePaths['structural_floor_plan_name_thumbnail'] ?? null,
                            'structural_floor_plan_pdf_path' => $filePaths['structural_floor_plan_name'] ?? null,
                            'structural_elevation_name' => $filePaths['structural_elevation_name_file'] ?? null,
                            'structural_elevation_view_path' => $filePaths['structural_elevation_name_thumbnail'] ?? null,
                            'structural_elevation_pdf_path' => $filePaths['structural_elevation_name'] ?? null,
                            'structural_sectional_name' => $filePaths['structural_sectional_name_file'] ?? null,
                            'structural_sectional_view_path' => $filePaths['structural_sectional_name_thumbnail'] ?? null,
                            'structural_sectional_pdf_path' => $filePaths['structural_sectional_name'] ?? null,
                            'structural_frame_diagram_name' => $filePaths['structural_frame_diagram_name_file'] ?? null,
                            'structural_frame_diagram_view_path' => $filePaths['structural_frame_diagram_name_thumbnail'] ?? null,
                            'structural_frame_diagram_pdf_path' => $filePaths['structural_frame_diagram_name'] ?? null,
                            'structural_diagram_all_name' => $filePaths['structural_diagram_all_name_file'] ?? null,
                            'structural_diagram_all_view_path' => $filePaths['structural_diagram_all_name_thumbnail'] ?? null,
                            'structural_diagram_all_pdf_path' => $filePaths['structural_diagram_all_name'] ?? null,
                        ]
                    );

                    // equipment_diagramデータの取得または更新
                    $drawing->equipment_diagram()->updateOrCreate(
                        ['drawing_id' => $drawing->id],
                        [
                            'machinery_equipment_diagram_all_name' => $filePaths['machinery_equipment_diagram_all_name_file'] ?? null,
                            'machinery_equipment_diagram_all_view_path' => $filePaths['machinery_equipment_diagram_all_name_thumbnail'] ?? null,
                            'machinery_equipment_diagram_all_pdf_path' => $filePaths['machinery_equipment_diagram_all_name'] ?? null,
                            'electrical_equipment_diagram_all_name' => $filePaths['electrical_equipment_diagram_all_name_file'] ?? null,
                            'electrical_equipment_diagram_all_view_path' => $filePaths['electrical_equipment_diagram_all_name_thumbnail'] ?? null,
                            'electrical_equipment_diagram_all_pdf_path' => $filePaths['electrical_equipment_diagram_all_name'] ?? null,
                        ]
                    );

                    // bim_drawingデータの取得または更新
                    $drawing->bim_drawing()->updateOrCreate(
                        ['drawing_id' => $drawing->id],
                        [
                            'bim_drawing_name' => $filePaths['bim_drawing_name_file'] ?? null,
                            'bim_drawing_view_path' => $filePaths['bim_drawing_name_thumbnail'] ?? null,
                            'bim_drawing_pdf_path' => $filePaths['bim_drawing_name'] ?? null
                        ]
                    );

                    // meeting_logデータの取得または更新
                    $project_name->meeting_log()->updateOrCreate(
                        ['project_id' => $project_name->id],
                        [
                            'meeting_log_name' => $filePaths['meeting_log_name_file'] ?? null,
                            'meeting_log_view_path' => $filePaths['meeting_log_name_thumbnail'] ?? null,
                            'meeting_log_pdf_path' => $filePaths['meeting_log_name'] ?? null,
                            'delivery_documents_name' => $filePaths['delivery_documents_name_file'] ?? null,
                            'delivery_documents_view_path' => $filePaths['delivery_documents_name_thumbnail'] ?? null,
                            'delivery_documents_pdf_path' => $filePaths['delivery_documents_name'] ?? null,
                            'bidding_documents_name' => $filePaths['bidding_documents_name_file'] ?? null,
                            'bidding_documents_view_path' => $filePaths['bidding_documents_name_thumbnail'] ?? null,
                            'bidding_documents_pdf_path' => $filePaths['bidding_documents_name'] ?? null,
                            'archived_photo_name' => $filePaths['archived_photo_name_file'] ?? null,
                            'archived_photo_view_path' => $filePaths['archived_photo_name_thumbnail'] ?? null,
                            'archived_photo_pdf_path' => $filePaths['archived_photo_name'] ?? null,
                            'contract_name' => $filePaths['contract_name_file'] ?? null,
                            'contract_view_path' => $filePaths['contract_name_thumbnail'] ?? null,
                            'contract_pdf_path' => $filePaths['contract_name'] ?? null,
                            'management_documents_name' => $filePaths['management_documents_name_file'] ?? null,
                            'management_documents_view_path' => $filePaths['management_documents_name_thumbnail'] ?? null,
                            'management_documents_pdf_path' => $filePaths['management_documents_name'] ?? null,
                        ],
                    );
                    Log::info('保存されたデータ', $result->toArray());
                } catch (\Exception $e) {
                    Log::error('トランザクションエラー:', ['message' => $e->getMessage()]);
                    throw $e;
                }
            });
            // 保存後のリダイレクト
            return response()->json(
                [
                    'message' => 'ファイルパスが保存されました！upload',
                    'file_paths' => $filePaths,
                ]
            );


            //                 // 成功時のレスポンス
            //                 return response()->json(
            //                     [
            //                         'message' => 'ファイルパスが保存されました！upload',
            //                         'file_paths' => $filePaths,
            //                     ],
            //                     201
            //                 );
            //             } catch (\Exception $e) {
            //                 // エラーログとレスポンス
            //                 Log::error("エラーupload 失敗: " . $e->getMessage());
            //                 return response()->json(['error' => 'ファイルの処理中にエラーが発生しました。upload'], 500);
            //             }
            //         });

            //         // 保存後のリダイレクト
            //         return response()->json(
            //             [
            //                 'message' => 'ファイルパスが保存されました！upload',
            //                 'file_paths' => $filePaths,
            //             ],
            //             201
            //         );
            //         // ダウンロード用リンクの返却
            //         // return response()->json([
            //         //     'message' => 'ファイルが正常にアップロードされましたデータベース',
            //         //     'file_id' => $file->id,
            //         //     'download_url' => route('download', ['id' => $file->id]),
            //         // ]);
        } catch (\Exception $e) {
            Log::error("エラーupload: " . $e->getMessage());
            return response()->json(['error' => 'ファイルの処理中にエラーが発生しました。upload'], 500);
        }
    }

    //ダウンロードdownloagメソッド
    // public function download($id)
    // {
    //      Log::info('情報メッセージextraction: 変数の値は', ['変数名' => $id]);

    //     `$id` に基づいて特定のプロジェクトを取得
    //     $project = project_name::with([
    //         'drawing.design_drawing',
    //         'drawing.structural_diagram',
    //         'drawing.equipment_diagram',
    //         'drawing.bim_drawing',
    //         'meeting_log',
    //     ])->findOrFail($id);


    //     $filePath = storage_path('app/public/' . $project->project_name); // 適切なフィールドを指定
    //     if (!file_exists($filePath)) {
    //         return response()->json(['message' => 'File not found'], 404);
    //     }

    //     return response()->download($filePath);
    // }




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
        // `$id` に基づいて特定のプロジェクトを取得
        $projectName = project_name::with([
            'drawing.design_drawing',
            'drawing.structural_diagram',
            'drawing.equipment_diagram',
            'drawing.bim_drawing',
            'meeting_log',
        ])->find($id);

        //Log::info('取得したプロジェクト$projectName:', ['$projectName' => $projectName]);

        // プロジェクトが存在しない場合の処理
        if (!$projectName) {
            Log::error('指定されたIDに対応するプロジェクトが見つかりません:', ['id' => $id]);
            return response()->json(['error' => 'プロジェクトが見つかりません'], 404);
        }

        $projectData = collect();

        // `drawing`が存在する場合のみ処理
        if ($projectName->drawing) {
            // `drawing`内の各リレーションをチェックして、空でない場合にデータを追加
            if (!empty($projectName->drawing->design_drawing)) {
                // Log::info('design_drawing:', ['design_drawing' => $projectName->drawing->design_drawing]);
                $projectData = $projectData->merge([$projectName->drawing->design_drawing]);
            }

            if (!empty($projectName->drawing->structural_diagram)) {
                //  Log::info('structural_diagram:', ['structural_diagram' => $projectName->drawing->structural_diagram]);
                $projectData = $projectData->merge([$projectName->drawing->structural_diagram]);
            }

            if (!empty($projectName->drawing->equipment_diagram)) {
                //   Log::info('equipment_diagram:', ['equipment_diagram' => $projectName->drawing->equipment_diagram]);
                $projectData = $projectData->merge([$projectName->drawing->equipment_diagram]);
            }

            if (!empty($projectName->drawing->bim_drawing)) {
                //  Log::info('bim_drawing:', ['bim_drawing' => $projectName->drawing->bim_drawing]);
                $projectData = $projectData->merge([$projectName->drawing->bim_drawing]);
            }
        }

        // `meeting_log`が存在する場合のみ処理
        if ($projectName->meeting_log) {
            // Log::info('meeting_log:', ['meeting_log' => $projectName->meeting_log]);
            $projectData = $projectData->merge([$projectName->meeting_log]);
        }

        // コレクションの内容を配列に変換してログに出力
        Log::info('取得したプロジェクトproject:', ['project' => $projectData]);

        // 正しいデータを返却
        return response()->json([
            'redirect' => 'Project_name/download',
            'filteredData' => $projectData, // 配列として返す
        ]);
    }



    //     // 部分一致検索を実行
    //     $project_name = project_name::where('id', $id)->with(['drawing.design_drawing', 'drawing.structural_diagram', 'drawing.equipment_diagram', 'drawing.bim_drawing', 'meeting_log'])->firstOrFail();
    //     Log::info('情報メッセージextraction: 変数の値は', ['変数名' => $project_name]);
    //     Log::info('情報メッセージextraction: $project_name', ['変数名' => $project_name]);ath' のカラム名を持つデータをフィルタリングする関数
    // $filterViewPath = function ($items) {
    //     Log::info('フィルタリング開始items:', ['items' => $items]); // 渡されるitemsを確認

    //     return collect($items)->filter(function ($value, $key) { // keyとvalue両方を取得
    //         Log::info('フィルタリング中のitem:', ['key' => $key, 'value' => $value]);

    //         // '_view_path' または 'name' で終わるキーをチェック
    //         if (is_string($key) && (substr($key, -10) === '_view_path' || substr($key, -5) === '_name')) {
    //             Log::info('該当するキーを発見:', ['key' => $key, 'value' => $value]);
    //             return true; // フィルタリング対象のアイテムを保持
    //         }

    //         return false; // '_view_path' で終わらない場合は除外
    //     })->toArray(); // コレクションを配列に変換
    // };

    // Log::info('フィルタリング関数準備完了');

    // // プロジェクトデータを取得
    // Log::info('プロジェクトデータ取得前id:', ['id' => $id]);
    // $project = project_name::with([
    //     'drawing.design_drawing',
    //     'drawing.structural_diagram',
    //     'drawing.equipment_diagram',
    //     'drawing.bim_drawing',
    //     'meeting_log',
    // ])->findOrFail($id);

    // Log::info('プロジェクトデータ取得後project:', ['project' => $project]);

    // // 各リレーションに対してフィルタリングを適用し、URLを追加
    // $filteredData = [
    //     'design_drawing' => $filterViewPath($project->drawing->design_drawing ?? []),
    //     'structural_diagram' => $filterViewPath($project->drawing->structural_diagram ?? []),
    //     'equipment_diagram' => $filterViewPath($project->drawing->equipment_diagram ?? []),
    //     'bim_drawing' => $filterViewPath($project->drawing->bim_drawing ?? []),
    // ];

    //   // public/thumbnails/）に基づいてURLを変換
    // foreach ($filteredData as $key => $items) {
    //     foreach ($items as $itemKey => $itemValue) {
    //         // パス情報のみをURLに変換
    //         if (strpos($itemValue, 'thumbnails/') !== false) {
    //             // サーバー上のURLを動的に生成
    //             $filteredData[$key][$itemKey] = url('storage/' . str_replace('public/', '', $itemValue)); // URL変換
    //         } else {
    //             // パス情報がURLでない場合も修正
    //             $filteredData[$key][$itemKey] = $itemValue;
    //         }

    //         // バックスラッシュ（￥）をスラッシュに変換
    //         $filteredData[$key][$itemKey] = str_replace('\\', '/', $filteredData[$key][$itemKey]);

    //         // 最後のバックスラッシュが残っている場合を削除
    //         $filteredData[$key][$itemKey] = rtrim($filteredData[$key][$itemKey], '\\');
    //     }
    // }
    //         // フィルタリング後のデータをJSON形式でログに出力 (エスケープを防ぐ)
    //         $jsonData = json_encode($filteredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    //         // バックスラッシュが残っている場合、それを取り除く
    //         $jsonData = stripslashes($jsonData);

    //         // 修正したデータをログに出力
    //         Log::info('フィルタリング後のデータ (JSON):', ['filteredData' => $jsonData]);
    //         // フィルタリング後のデータをそのまま配列としてログに出力
    //         Log::info('フィルタリング後のデータfilteredData:', ['filteredData' => $filteredData]);
    //         // バックスラッシュが含まれていないか確認
    //         //Log::info('フィルタリング後の生データ:', ['filteredData' => print_r($filteredData, true)]);

    //         // 変換後のデータを格納する配列
    //         $converted_projects = [];

    //         // 変換処理
    //         foreach ($filteredData as $key => $value) {
    //             foreach ($value as $sub_key => $sub_value) {
    //                 if (str_contains($sub_key, "name")) { // "name"が含まれるキーの場合
    //                     $file_name = $sub_value;
    //                     // 対応する "view_path" を取得
    //                     $view_path_key = str_replace("name", "view_path", $sub_key);
    //                     if (array_key_exists($view_path_key, $value)) {
    //                         // 新しいキーを作成
    //                         $new_key = "{$key}.{$file_name}";
    //                         $new_value = $value[$view_path_key];
    //                         // 新しいキーと値を配列に追加
    //                         $converted_projects[$new_key] = $new_value;
    //                     }
    //                 }
    //             }
    //         }
    //         Log::info('フィルタリング後のデータ$converted_projects:', ['converted_projects' => $converted_projects]);


    // フィルタリング後のデータをJSON形式でログに出力 (エスケープを防ぐ)
    // Log::info('フィルタリング後のデータ (JSON):', [
    //     'filteredData' => json_encode($filteredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    // ]);

    // フィルタリングされたデータをレスポンスとして返却
    //     return response()->json([
    //         'redirect' => 'Project_name/download',
    //         'filteredData' => $converted_projects, // '_view_path' のみ抽出されたデータ（URL付き）
    //     ]);
    // }



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
