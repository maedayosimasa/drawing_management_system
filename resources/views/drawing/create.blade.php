<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>図面</title>
</head>
<body>
        <h2>図面作成</h2>

 

    <!-- フォーム -->
    <form method="post" action="{{route('drawing.store')}}">
        @csrf

        <!-- Project ID入力 -->
        <div>
            <label for="project_name_id">Project Name ID:</label>
            <input type="text" name="project_name_id" id="project_name_id" required>
        </div> 
        
        <!-- Meeting Log Name入力 -->
        {{-- <div>
            <label for="drawing_id">Drawing:</label>
            <input type="text" name="drawing_id" id="drawing_id" required>
        </div> --}}

        <!-- 送信ボタン -->
        <div>
            <button type="submit">送信</button>
        </div>
    </form>
</body>
</html>