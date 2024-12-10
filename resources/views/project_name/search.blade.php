<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>プロジェクトの検索</title>

    <style>
        body {
            font-family: 'Georgia', serif; /* 高級感のある書体 */
            background-color: #f8f8f8; /* 淡いグレーの背景 */
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #b38b5d; /* ゴールドに近い色 */
            margin-top: 40px;
            font-weight: bold;
            letter-spacing: 3px; /* 文字間隔を広げてエレガントに */
            text-shadow: 3px 3px 6px rgba(184, 134, 11, 0.5); /* ゴールドの輝きを感じさせる影 */
            font-family: 'Garamond', serif; /* より高級感のある書体 */
        }

        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #faf1d7; /* ゴールド系の優しい色合い */
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1); /* 繊細な影 */
            border-radius: 20px;
            border: 0.5px solid #d4af37; /* ゴールドの枠線を細く */
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 1.2rem;
            font-weight: bold;
            color: #6e4b3b; /* 高級感のあるゴールドブラウン */
            display: block;
            margin-bottom: 10px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 1.2rem;
            color: #333;
            background: #fff;
            border: 1px solid #d4af37; /* ゴールドのボーダー */
            border-radius: 10px;
            box-sizing: border-box;
            transition: border 0.3s ease-in-out;
        }

        .form-group input:focus {
            border-color: #b38b5d; /* フォーカス時に濃いゴールド */
            outline: none;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            color: #fff;
            background: #d4af37; /* ゴールドの背景 */
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .submit-btn:hover {
            background: #b38b5d; /* ホバー時に少しダークゴールドに */
        }

        .error-message {
            color: red;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .error-message ul {
            list-style-type: none;
            padding: 0;
        }

        .error-message ul li {
            margin: 5px 0;
        }

        .result-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            border: 0.5px solid #d4af37;
        }

        .result-container ul {
            padding-left: 20px;
            font-size: 1.2rem;
        }

        .result-container ul li {
            list-style-type: disc;
            margin-bottom: 10px;
        }
    </style>

</head>
<body>
    <h1>プロジェクトの検索</h1>

    <!-- 検索フォーム -->
    <div class="form-container">
        <form action="{{ route('project_name.search') }}" method="get">
            <div class="form-group">
                <label for="query">検索項目</label>
                <input type="text" name="query" id="query" value="{{ old('query', $query ?? '') }}" placeholder="検索 project name..." required>
            </div>
            <button type="submit" class="submit-btn">検索</button>
        </form>
    </div>

    <!-- 検索結果表示 -->
    @if(isset($project_name) && $project_name->isNotEmpty())
        <form action="{{ route('project_name.select') }}" method="post">
                @csrf
        <div class="result-container">
            <h4>検索結果:</h4>
            <ul>
                @foreach($project_name as $project_name)
                <li>
                    <input type="checkbox" name="project_name_id[]" value="{{$project_name->id}}"> 
                     {{ $project_name->project_name }}
                 </li>
                 <h2>  {{ $project_name->id??'idエラー' }}</h2>
                  @endforeach
            </ul>
             <button type="submit" class="submit-btn" name="action" value="select">選択</button>
        </div>
        </form>
    @elseif(isset($query))
        <p>見つかりませんでした: "{{ $query }}".</p>
    @endif
</body>
</html>
