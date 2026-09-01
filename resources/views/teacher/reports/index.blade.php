<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教員用・匿名練習報告一覧</title>
</head>
<body>
    <main>
        <h1>教員用・匿名練習報告一覧</h1>

        <p>
            これはLaravelを学ぶための匿名練習用一覧です。実在する学生の情報は扱いません。
            この画面にはログイン機能がないため、本番運用には使用しません。学生用画面との移動は、練習用の画面移動です。
        </p>

        <p>
            <a href="{{ route('practice-reports.create') }}">学生用フォームへ戻る</a>
        </p>

        <section aria-label="サーバーの状態確認">
            <h2>サーバーの状態確認</h2>
            <button type="button" id="health-check-button">サーバーの状態を確認する</button>
            <p id="health-check-result" role="status" aria-live="polite"></p>
        </section>

        <section aria-label="サーバーの匿名練習報告の初期化">
            <h2>サーバーの匿名練習報告を初期化する</h2>
            <p>この操作はdatabase.sqlite内の匿名練習報告だけを削除します。</p>
            <button type="button" id="server-reports-reset-button">サーバーの匿名練習報告を初期化する</button>
            <p id="server-reports-reset-result" role="status" aria-live="polite"></p>
        </section>

        <p>
            「要確認」は確認を助けるための匿名練習用の目印です。緊急性を自動判定する機能ではありません。
        </p>

        <section aria-label="匿名練習報告の集計">
            <h2>集計</h2>
            <ul>
                <li>全ての報告数：{{ $totalReportCount }} 件</li>
                <li>未対応の報告数：{{ $unhandledReportCount }} 件</li>
                <li>対応済みの報告数：{{ $handledReportCount }} 件</li>
            </ul>
        </section>

        <nav aria-label="匿名練習報告の絞り込み">
            <a href="{{ route('teacher.reports.index') }}" @if ($filter === 'all') aria-current="page" @endif>全て表示</a>
            <a href="{{ route('teacher.reports.index', ['filter' => 'unhandled']) }}" @if ($filter === 'unhandled') aria-current="page" @endif>未対応のみ</a>
            <a href="{{ route('teacher.reports.index', ['filter' => 'handled']) }}" @if ($filter === 'handled') aria-current="page" @endif>対応済みのみ</a>
            <a href="{{ route('teacher.reports.index', ['filter' => 'needs-review']) }}" @if ($filter === 'needs-review') aria-current="page" @endif>要確認のみ</a>
        </nav>

        @if ($reports->isEmpty())
            <p>匿名練習報告はまだありません。</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">体調</th>
                        <th scope="col">予定への参加</th>
                        <th scope="col">教員からの連絡希望</th>
                        <th scope="col">対応状態</th>
                        <th scope="col">対応状態を変更した日時</th>
                        <th scope="col">報告日時</th>
                        <th scope="col">確認</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->condition }}</td>
                            <td>{{ $report->attendance }}</td>
                            <td>{{ $report->contact_request }}</td>
                            <td>
                                <span>{{ $report->status }}</span>

                                @if ($report->status === '未対応')
                                    <form method="POST" action="{{ route('teacher.reports.update-status', $report) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="対応済み">
                                        <button type="submit">対応済みにする</button>
                                    </form>

                                @else
                                    <form method="POST" action="{{ route('teacher.reports.update-status', $report) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="未対応">
                                        <button type="submit">未対応へ戻す</button>
                                    </form>
                                @endif

                                <button
                                    type="button"
                                    data-api-status-button
                                    data-report-id="{{ $report->id }}"
                                    data-new-status="{{ $report->status === '未対応' ? '対応済み' : '未対応' }}"
                                >
                                    @if ($report->status === '未対応')
                                        APIで対応済みにする
                                    @else
                                        APIで未対応へ戻す
                                    @endif
                                </button>
                                <p id="api-status-result-{{ $report->id }}" role="status" aria-live="polite"></p>
                            </td>
                            <td>{{ $report->status_changed_at?->format('Y年m月d日 H:i') ?? '変更なし' }}</td>
                            <td>{{ $report->created_at?->format('Y年m月d日 H:i') }}</td>
                            <td>
                                @if ($report->condition === '不良' || $report->contact_request === '希望する')
                                    <strong>要確認</strong>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>

    <script>
        const healthCheckButton = document.getElementById('health-check-button');
        const healthCheckResult = document.getElementById('health-check-result');
        const serverReportsResetButton = document.getElementById('server-reports-reset-button');
        const serverReportsResetResult = document.getElementById('server-reports-reset-result');
        const apiStatusButtons = document.querySelectorAll('[data-api-status-button]');

        healthCheckButton.addEventListener('click', async () => {
            healthCheckButton.disabled = true;
            healthCheckResult.textContent = 'サーバーを確認しています。';

            try {
                const response = await fetch('/api/health');

                if (! response.ok) {
                    throw new Error('ヘルスチェックに失敗しました。');
                }

                const data = await response.json();

                if (data.status === 'ok') {
                    healthCheckResult.textContent = 'サーバーは動いています。';
                } else {
                    throw new Error('想定と違う返事です。');
                }
            } catch (error) {
                healthCheckResult.textContent = 'サーバーに接続できません。http://localhost:8000/ を開き、php artisan serveでLaravelサーバーが起動しているか確認してください。';
            } finally {
                healthCheckButton.disabled = false;
            }
        });

        serverReportsResetButton.addEventListener('click', async () => {
            const isConfirmed = window.confirm('サーバーに保存されている匿名練習報告をすべて初期化します。実在の個人情報は扱いませんが、元に戻せません。続けますか？');

            if (! isConfirmed) {
                return;
            }

            let willReload = false;
            serverReportsResetButton.disabled = true;
            serverReportsResetResult.textContent = '匿名練習報告を初期化しています。';

            try {
                const response = await fetch('/api/reports', {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (! response.ok) {
                    throw new Error('初期化に失敗しました。');
                }

                const data = await response.json();
                serverReportsResetResult.textContent = `匿名練習報告を${data.deletedCount}件初期化しました。一覧を読み直します。`;
                willReload = true;

                window.setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } catch (error) {
                serverReportsResetResult.textContent = 'サーバーに接続できません。http://localhost:8000/ を開き、php artisan serveでLaravelサーバーが起動しているか確認してください。';
            } finally {
                if (! willReload) {
                    serverReportsResetButton.disabled = false;
                }
            }
        });

        apiStatusButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const reportId = button.dataset.reportId;
                const newStatus = button.dataset.newStatus;
                const result = document.getElementById(`api-status-result-${reportId}`);
                let willReload = false;

                button.disabled = true;
                result.textContent = 'APIで対応状態を変更しています。';

                try {
                    const response = await fetch(`/api/reports/${reportId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            status: newStatus,
                        }),
                    });
                    const responseData = await response.json();

                    if (response.status !== 200) {
                        result.textContent = responseData.message
                            ?? 'APIで対応状態を変更できませんでした。';
                        return;
                    }

                    result.textContent = `APIで報告番号${responseData.id}を${responseData.status}にしました。一覧を読み直します。`;
                    willReload = true;

                    window.setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } catch (error) {
                    result.textContent = 'APIへ接続できません。http://localhost:8000/ を開き、php artisan serveでLaravelサーバーが起動しているか確認してください。';
                } finally {
                    if (! willReload) {
                        button.disabled = false;
                    }
                }
            });
        });
    </script>
</body>
</html>
