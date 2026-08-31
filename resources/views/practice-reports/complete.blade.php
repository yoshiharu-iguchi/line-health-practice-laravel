<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>匿名練習報告を確認しました</title>
</head>
<body>
    <main>
        <h1>匿名練習報告を確認しました</h1>

        <p>
            匿名練習報告をSQLiteへ保存しました。実在する学生の情報は保存していません。
        </p>

        <p>SQLiteに保存されている匿名練習報告は、現在 {{ $reportCount }} 件です。</p>

        <a href="{{ route('practice-reports.create') }}">新しい匿名練習報告を入力する</a>
    </main>
</body>
</html>
