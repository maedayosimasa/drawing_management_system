<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>設備面</title>
</head>
<body>
        <h2>設備面作成</h2>

 

    <!-- フォーム -->
    <form method="post" action="{{route('equipment_diagram.store')}}">
        @csrf

        <!-- Project ID入力 -->
        <div>
            <label for="drawing_id">Drawing ID:</label>
            <input type="text" name="drawing_id" id="drawing_id" required>
        </div> 
        
        <!-- Meeting Log Name入力 -->
        <div>
            <label for="machinery_equipment_diagram_all_name">Machinery Equipment Diagram All Name</label>
            <input type="text" name="machinery_equipment_diagram_all_name" id="machinery_equipment_diagram_all_name" required>
        </div>

        <!-- 送信ボタン -->
        <div>
            <button type="submit">送信</button>
        </div>
    </form>
</body>
</html>