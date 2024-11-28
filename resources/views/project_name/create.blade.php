<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>プロジェクト名</title>
    <style>
        body {
            font-family: 'Georgia', serif; /* 高級感のある書体 */
            background-color: #f8f8f8; /* 淡いグレーの背景 */
            color: #333;
            margin: 0;
            padding: 0;
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
    </style>
</head>
<body>

    <h2>プロジェクトの作成</h2>

    <!-- エラーメッセージの表示 -->
    {{-- @if ($errors->any()) --}}
    {{--    <div class="error-message"> --}}
    {{--        <ul> --}}
    {{--            @foreach ($errors->all() as $error) --}}
    {{--                <li>{{ $error }}</li> --}}
    {{--            @endforeach --}}
    {{--        </ul> --}}
    {{--    </div> --}}
    {{-- @endif --}}

    <!-- フォーム -->
    <div class="form-container">
        <form method="post" action="{{ route('project_name.store') }}">
            @csrf

            <!-- User ID入力 -->
            <div class="form-group">
                <label for="user_id">User ID:</label>
                <input type="text" name="user_id" id="user_id" required>
            </div>

            <!-- Project Name入力 -->
            <div class="form-group">
                <label for="project_name">Project Name:</label>
                <input type="text" name="project_name" id="project_name" required>
            </div>

            <!-- 送信ボタン -->
            <div>
                <button type="submit" class="submit-btn">送信</button>
            </div>
        </form>
    </div>

</body>
</html>
