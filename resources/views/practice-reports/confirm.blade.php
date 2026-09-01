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

        <section aria-labelledby="api-practice-title">
            <h2 id="api-practice-title">API送信の練習</h2>
            <p>
                このボタンはAPI学習用です。「この内容で完了する」と両方を押すと、同じ匿名練習報告が2件保存される可能性があります。練習では、どちらか一方だけを押してください。
            </p>

            <button type="button" id="api-report-submit-button">APIで匿名練習報告を送る</button>
            <p id="api-report-result" role="status" aria-live="polite"></p>
        </section>
    </main>

    <script>
        const apiReportSubmitButton = document.getElementById('api-report-submit-button');
        const apiReportResult = document.getElementById('api-report-result');
        const reportForApi = @json($report);

        apiReportSubmitButton.addEventListener('click', async () => {
            let apiReportWasSaved = false;

            apiReportSubmitButton.disabled = true;
            apiReportResult.textContent = 'APIへ送信しています。';

            try {
                const response = await fetch('/api/reports', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        condition: reportForApi.condition,
                        attendance: reportForApi.attendance,
                        contactRequest: reportForApi.contactRequest,
                    }),
                });
                const responseData = await response.json();

                if (response.status === 201) {
                    const createdAt = new Date(responseData.createdAt).toLocaleString('ja-JP');

                    apiReportWasSaved = true;
                    apiReportSubmitButton.textContent = 'API送信済み';
                    apiReportResult.textContent = `APIへ匿名練習報告を送信しました。報告番号：${responseData.id}、対応状態：${responseData.status}、報告日時：${createdAt}`;
                    return;
                }

                apiReportResult.textContent = responseData.message
                    ?? 'APIへ送信できませんでした。選択内容とサーバーの状態を確認してください。';
            } catch (error) {
                apiReportResult.textContent = 'APIへ接続できません。http://localhost:8000/ を開き、php artisan serveでLaravelサーバーが起動しているか確認してください。';
            } finally {
                if (!apiReportWasSaved) {
                    apiReportSubmitButton.disabled = false;
                }
            }
        });
    </script>
</body>
</html>
