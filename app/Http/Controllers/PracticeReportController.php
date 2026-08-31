<?php

namespace App\Http\Controllers;

use App\Models\PracticeReport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PracticeReportController extends Controller
{
    /**
     * 匿名練習用の学生報告フォームを表示します。
     * この段階では、入力内容の保存や送信は行いません。
     */
    public function create(): View
    {
        return view('practice-reports.create');
    }

    /**
     * 入力内容が決めた選択肢かを確認し、保存せず確認画面に表示します。
     */
    public function confirm(Request $request): View
    {
        $report = $this->validatePracticeReport($request);

        return view('practice-reports.confirm', ['report' => $report]);
    }

    /**
     * 確認済みの匿名練習内容をSQLiteへ保存し、完了画面を表示します。
     */
    public function complete(Request $request): View
    {
        $report = $this->validatePracticeReport($request);

        PracticeReport::create([
            'condition' => $report['condition'],
            'attendance' => $report['attendance'],
            'contact_request' => $report['contactRequest'],
        ]);

        $reportCount = PracticeReport::count();

        return view('practice-reports.complete', ['reportCount' => $reportCount]);
    }

    /**
     * 教員用に、匿名練習報告を報告日時の新しい順で表示します。
     */
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');

        if (! in_array($filter, ['all', 'unhandled', 'handled', 'needs-review'], true)) {
            $filter = 'all';
        }

        $reportsQuery = PracticeReport::query()
            ->orderByRaw("CASE status WHEN '未対応' THEN 0 WHEN '対応済み' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at');

        if ($filter === 'unhandled') {
            $reportsQuery->where('status', '未対応');
        }

        if ($filter === 'handled') {
            $reportsQuery->where('status', '対応済み');
        }

        if ($filter === 'needs-review') {
            $reportsQuery->where(function ($query) {
                $query->where('condition', '不良')
                    ->orWhere('contact_request', '希望する');
            });
        }

        $reports = $reportsQuery->get();
        $totalReportCount = PracticeReport::count();
        $unhandledReportCount = PracticeReport::where('status', '未対応')->count();
        $handledReportCount = PracticeReport::where('status', '対応済み')->count();

        return view('teacher.reports.index', [
            'reports' => $reports,
            'totalReportCount' => $totalReportCount,
            'unhandledReportCount' => $unhandledReportCount,
            'handledReportCount' => $handledReportCount,
            'filter' => $filter,
        ]);
    }

    /**
     * 指定された匿名練習報告の対応状態だけを更新します。
     */
    public function updateStatus(Request $request, PracticeReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:未対応,対応済み'],
        ]);

        if ($report->status !== $validated['status']) {
            $report->update([
                'status' => $validated['status'],
                'status_changed_at' => now(),
            ]);
        }

        return redirect()->route('teacher.reports.index');
    }

    /**
     * 匿名練習用フォームで使う3項目を確認します。
     *
     * @return array<string, string>
     */
    private function validatePracticeReport(Request $request): array
    {
        return $request->validate([
            'condition' => ['required', 'in:良好,普通,不良'],
            'attendance' => ['required', 'in:参加できる,相談したい,参加が難しい'],
            'contactRequest' => ['required', 'in:不要,希望する'],
        ]);
    }
}
