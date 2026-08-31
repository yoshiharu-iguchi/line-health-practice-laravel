<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>匿名練習用・報告内容の確認</title>
</head>
<body>
    <main>
        <h1>報告内容の確認</h1>

        <p>
            これは匿名の練習用確認画面です。まだ保存も送信もされていません。
        </p>

        <dl>
            <dt>体調</dt>
            <dd>{{ $report['condition'] }}</dd>

            <dt>予定への参加</dt>
            <dd>{{ $report['attendance'] }}</dd>

            <dt>教員からの連絡希望</dt>
            <dd>{{ $report['contactRequest'] }}</dd>
        </dl>

        <form method="GET" action="{{ route('practice-reports.create') }}">
            <button type="submit">入力を修正する</button>
        </form>

        <form method="POST" action="{{ route('practice-reports.complete') }}">
            @csrf
            <input type="hidden" name="condition" value="{{ $report['condition'] }}">
            <input type="hidden" name="attendance" value="{{ $report['attendance'] }}">
            <input type="hidden" name="contactRequest" value="{{ $report['contactRequest'] }}">

            <button type="submit">この内容で完了する</button>
        </form>
    </main>
</body>
</html>
