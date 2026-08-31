<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>匿名練習用・体調報告フォーム</title>
</head>
<body>
    <main>
        <h1>匿名練習用・体調報告フォーム</h1>

        <p>
            これはLaravelを学ぶための練習画面です。実在する学生の名前や体調情報は入力しません。
            入力内容は確認画面で確認後、「この内容で完了する」を押したときだけ、匿名練習報告として保存されます。
        </p>

        <p>
            <a href="{{ route('teacher.reports.index') }}">教員用画面を開く</a>
        </p>

        <p>
            このリンクは練習用の画面移動です。教員用画面を守るログイン機能ではありません。
        </p>

        <form method="POST" action="{{ route('practice-reports.confirm') }}">
            @csrf

            <div>
                <label for="condition">体調</label>
                <select id="condition" name="condition" aria-describedby="condition-error">
                    <option value="" @selected(old('condition', '') === '')>選択してください</option>
                    <option value="良好" @selected(old('condition') === '良好')>良好</option>
                    <option value="普通" @selected(old('condition') === '普通')>普通</option>
                    <option value="不良" @selected(old('condition') === '不良')>不良</option>
                </select>

                @error('condition')
                    <p id="condition-error">体調を選んでください。</p>
                @enderror
            </div>

            <div>
                <label for="attendance">予定への参加</label>
                <select id="attendance" name="attendance" aria-describedby="attendance-error">
                    <option value="" @selected(old('attendance', '') === '')>選択してください</option>
                    <option value="参加できる" @selected(old('attendance') === '参加できる')>参加できる</option>
                    <option value="相談したい" @selected(old('attendance') === '相談したい')>相談したい</option>
                    <option value="参加が難しい" @selected(old('attendance') === '参加が難しい')>参加が難しい</option>
                </select>

                @error('attendance')
                    <p id="attendance-error">予定への参加を選んでください。</p>
                @enderror
            </div>

            <div>
                <label for="contact-request">教員からの連絡希望</label>
                <select id="contact-request" name="contactRequest" aria-describedby="contact-request-error">
                    <option value="" @selected(old('contactRequest', '') === '')>選択してください</option>
                    <option value="不要" @selected(old('contactRequest') === '不要')>不要</option>
                    <option value="希望する" @selected(old('contactRequest') === '希望する')>希望する</option>
                </select>

                @error('contactRequest')
                    <p id="contact-request-error">教員からの連絡希望を選んでください。</p>
                @enderror
            </div>

            <button type="submit">入力内容を確認する</button>
        </form>
    </main>
</body>
</html>
