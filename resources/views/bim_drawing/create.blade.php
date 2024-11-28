<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BIM図面</title>
</head>
<body>
        <h2>BIM図面作成</h2>

 

    <!-- フォーム -->
    <form method="post" action="{{route('bim_drawing.store')}}">
        @csrf

        <!-- Project ID入力 -->
        <div>
            <label for="drawing_id">Drawing ID:</label>
            <input type="text" name="drawing_id" id="drawing_id" required>
        </div> 
        
        <!-- Meeting Log Name入力 -->
        <div>
            <label for="bim_drawing_name">Bim Drawing Name:</label>
            <input type="text" name="bim_drawing_name" id="bim_drawing_name" required>
        </div>

        <!-- 送信ボタン -->
        <div>
            <button type="submit">送信</button>
        </div>
    </form>
</body>
</html>