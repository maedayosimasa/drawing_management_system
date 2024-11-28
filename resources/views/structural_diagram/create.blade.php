<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>構造図面</title>
</head>
<body>
        <h2>構造図面作成</h2>

 

    <!-- フォーム -->
    <form method="post" action="{{route('structural_diagram.store')}}">
        @csrf

        <!-- Project ID入力 -->
        <div>
            <label for="drawing_id">Drawing ID:</label>
            <input type="text" name="drawing_id" id="drawing_id" required>
        </div> 
        
        <!-- Meeting Log Name入力 -->
        <div>
            <label for="floor_plan_name">Floor Plan Name:</label>
            <input type="text" name="floor_plan_name" id="floor_plan_name" required>
        </div>

        <!-- 送信ボタン -->
        <div>
            <button type="submit">送信</button>
        </div>
    </form>
</body>
</html>