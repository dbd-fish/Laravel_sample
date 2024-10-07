<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク管理システム</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <!-- ヘッダー部分 -->
    <header class="bg-dark text-white py-4 mb-4">
        <div class="container text-center">
            <h1 class="display-4">タスク管理システム</h1>
        </div>
    </header>

    <!-- メインコンテンツ部分 -->
    <main class="container">
        <div class="bg-white p-4 rounded shadow-sm">
            @yield('content')
        </div>
    </main>
</body>
</html>
