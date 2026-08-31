<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PracticeReportController;

// 最初に開く画面は、匿名練習用の学生入力フォームにします。
Route::redirect('/', '/student/report');

// 学生が匿名の練習用フォームを開くためのURLです。
Route::get('/student/report', [PracticeReportController::class, 'create'])
    ->name('practice-reports.create');

// フォームの選択内容を受け取り、保存せず確認画面に表示します。
Route::post('/student/report/confirm', [PracticeReportController::class, 'confirm'])
    ->name('practice-reports.confirm');

// 確認済みの練習内容を保存せず、完了画面に表示します。
Route::post('/student/report/complete', [PracticeReportController::class, 'complete'])
    ->name('practice-reports.complete');

// 教員が匿名練習報告を読むための一覧画面です。ログイン機能はまだありません。
Route::get('/teacher/reports', [PracticeReportController::class, 'index'])
    ->name('teacher.reports.index');

// 指定した匿名練習報告の対応状態だけを更新します。
Route::patch('/teacher/reports/{report}/status', [PracticeReportController::class, 'updateStatus'])
    ->name('teacher.reports.update-status');

// サーバーが返事をできるかだけを確認する、データを含まないヘルスチェックです。
Route::get('/api/health', function () {
    return response()->json(['status' => 'ok']);
});
