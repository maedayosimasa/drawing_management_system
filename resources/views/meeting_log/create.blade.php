<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>管理書類</title>
</head>
<body>
        <h2>管理書類作成</h2>

 

    <!-- フォーム -->
    <form method="post" action="{{route('meeting_log.store')}}">
        @csrf

        <!-- Project ID入力 -->
        <div>
            <label for="project_id">Project ID:</label>
            <input type="text" name="project_id" id="project_id" required>
        </div> 
        
        <!-- Meeting Log Name入力 -->
        <div>
            <label for="meeting_log_name">Meeting Name:</label>
            <input type="text" name="meeting_log_name" id="meeting_log_name" required>
        </div>

        <!-- 送信ボタン -->
        <div>
            <button type="submit">送信</button>
        </div>
    </form>
</body>
</html>