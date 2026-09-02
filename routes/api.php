<?php

use App\Http\Controllers\Api\PracticeReportController;
use Illuminate\Support\Facades\Route;

// ローカルの匿名練習報告だけを読むAPIです。ログイン機能はありません。
Route::get('/reports', [PracticeReportController::class, 'index'])
    ->name('api.practice-reports.index');

// 匿名練習報告の件数だけをJSONで返すAPIです。
Route::get('/reports/summary', [PracticeReportController::class, 'summary'])
    ->name('api.practice-reports.summary');

// 指定した番号の匿名練習報告を1件だけJSONで返すAPIです。
Route::get('/reports/{id}', [PracticeReportController::class, 'show'])
    ->name('api.practice-reports.show');

// ローカルの匿名練習報告だけをJSONで受け取り、SQLiteへ保存するAPIです。
Route::post('/reports', [PracticeReportController::class, 'store'])
    ->name('api.practice-reports.store');

// 指定した匿名練習報告の対応状態だけを更新するAPIです。
Route::patch('/reports/{id}/status', [PracticeReportController::class, 'updateStatus'])
    ->name('api.practice-reports.update-status');

// database.sqlite内の匿名練習報告だけをすべて初期化するAPIです。
Route::delete('/reports', [PracticeReportController::class, 'destroyAll'])
    ->name('api.practice-reports.destroy-all');
