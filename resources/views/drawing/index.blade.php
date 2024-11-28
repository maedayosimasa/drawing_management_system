<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>プロジェクト名一覧</title>

    <style>
body {
    font-family: 'Georgia', serif; /* より優雅でおしゃれな書体 */
    background-color: #f8f8f8; /* 淡いグレーの背景 */
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: 50px auto;
    padding: 30px;
    background: #fcfbf8; /* ゴールド系の優しい色合い */
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1); /* 繊細な影 */
    border-radius: 20px;
    border: 0.5px solid #d4af37; /* ゴールドの枠線を細く（0.5px） */
    transition: all 0.3s ease-in-out;
}

.container:hover {
    transform: scale(1.02); /* ホバー時に少し拡大 */
}

h2 {
    text-align: center;
    font-size: 2.5rem;
    color: #b38b5d; /* ゴールドに近い色 */
    margin-bottom: 30px;
    font-weight: bold;
    letter-spacing: 3px; /* 文字間隔を広げてエレガントに */
    text-shadow: 3px 3px 6px rgba(184, 134, 11, 0.5); /* ゴールドの輝きを感じさせる影 */
    font-family: 'Garamond', serif; /* より高級感のある書体 */
}

.project-card {
    margin-bottom: 30px;
    padding: 25px;
    background: #fff8e1; /* やわらかい金色背景 */
    border: 0.5px solid #d4af37; /* ゴールドの境界線を細く（0.5px） */
    border-radius: 12px;
    transition: transform 0.3s, box-shadow 0.3s ease-in-out;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* 繊細な影 */
}

.project-card:hover {
    transform: translateY(-5px); /* カードが浮き上がるようなエフェクト */
    box-shadow: 0 15px 30px rgba(184, 134, 11, 0.3); /* 強調されたゴールドの影 */
}

.project-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #6e4b3b; /* 高級感のあるゴールドブラウン */
    margin-bottom: 10px;
    letter-spacing: 1px; /* 文字間隔を少し広げておしゃれに */
    text-transform: uppercase; /* 大文字にして強調 */
    border-bottom: 2px solid #d4af37; /* ゴールドの下線（細く） */
    padding-bottom: 5px; /* 下線との距離調整 */
    font-family: 'Times New Roman', serif; /* 伝統的で高級感のある書体 */
}

.project-content {
    font-size: 1.2rem;
    margin-top: 8px;
    color: #7f6b4e; /* 落ち着いたゴールドブラウン */
    line-height: 1.6;
    letter-spacing: 1px; /* 文字間隔を広げて、より洗練された印象 */
    font-family: 'Georgia', serif; /* おしゃれなセリフ体 */
}

hr {
    border: none;
    height: 1px; /* 線を細く */
    background: linear-gradient(to right, #d4af37, #b38b5d); /* ゴールドのグラデーションの線 */
    margin: 25px 0;
}

a {
    color: #b38b5d;
    text-decoration: none;
    font-weight: bold;
    padding: 10px 20px;
    border: 2px solid #b38b5d; /* ボーダー線を細く */
    border-radius: 25px;
    transition: background 0.3s, color 0.3s;
    letter-spacing: 1px; /* 文字間隔を広げて洗練された印象 */
    font-family: 'Garamond', serif; /* 高級感のある書体 */
}

a:hover {
    background: #d4af37;
    color: #fff;
    border-color: #d4af37; /* ホバー時に境界線の色も変更 */
}

    </style>
</head>
<body>
    <div class="container">
        <h2>図面覧の作成</h2>
        <div>
            {{-- @if($posts->isEmpty())
            <p>データが存在しません。</p>
        @else --}}

            
            @foreach ($posts as $drawing)

                <div class="project-card">
                    {{-- <h1 class="project-title">
                        ユーザーID: {{$drawing->project_name_id->user_id??'エラー'}}
                    </h1>
                    <hr> --}}
                    <p class="project-content">
                        プロジェクト名: {{$drawing->project_name->project_name??'エラー'}}
                    </p>
                     <hr>
                    <p class="project-content">
                        プロジェクトID: {{$drawing->project_name_id??'エラー'}}
                    </p>
                 
                </div>
            @endforeach
        </div>
    </div>
    
</body>
</html>
