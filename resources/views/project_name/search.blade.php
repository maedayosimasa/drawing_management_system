<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>プロジェクトの検索</title>
</head>
<body>
 @section('content')
    <form action="{{ route('project_name.search') }}" method="GET">
        <input type="text" name="query" placeholder="プロジェクト名を検索" required>
        <button type="submit">検索</button>
    </form>
@endsection
</body>
</html>