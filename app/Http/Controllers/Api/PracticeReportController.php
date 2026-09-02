<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PracticeReportResource;
use App\Models\PracticeReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PracticeReportController extends Controller
{
    /**
     * ローカルの匿名練習報告を、JSONとして読み取り専用で返します。
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $filter = $request->query('filter');

        if ($status !== null && ! in_array($status, ['未対応', '対応済み'], true)) {
            return response()->json([
                'message' => 'statusには「未対応」または「対応済み」を指定してください。',
            ], 422);
        }

        if ($filter !== null && $filter !== 'needs-review') {
            return response()->json([
                'message' => 'filterには「needs-review」を指定してください。',
            ], 422);
        }

        $query = PracticeReport::query();

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($filter === 'needs-review') {
            $query->where(function ($needsReviewQuery) {
                $needsReviewQuery
                    ->where('condition', '不良')
                    ->orWhere('contact_request', '希望する');
            });
        }

        $reports = $query
            ->orderByRaw("CASE status WHEN '未対応' THEN 0 WHEN '対応済み' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->get();

        return response()->json(PracticeReportResource::collection($reports)->resolve());
    }

    /**
     * ローカルの匿名練習報告の件数を、JSONとして読み取り専用で返します。
     */
    public function summary(): JsonResponse
    {
        return response()->json([
            'totalReportCount' => PracticeReport::query()->count(),
            'unhandledReportCount' => PracticeReport::query()
                ->where('status', '未対応')
                ->count(),
            'handledReportCount' => PracticeReport::query()
                ->where('status', '対応済み')
                ->count(),
        ]);
    }

    /**
     * 指定した番号の匿名練習報告を、JSONで1件だけ返します。
     */
    public function show(int $id): JsonResponse
    {
        $report = PracticeReport::find($id);

        if ($report === null) {
            return response()->json([
                'message' => '指定した匿名練習報告が見つかりません。',
            ], 404);
        }

        return response()->json((new PracticeReportResource($report))->resolve());
    }

    /**
     * 匿名練習報告のJSONを確認し、正しい選択肢だけをSQLiteへ保存します。
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            'condition' => ['required', 'in:良好,普通,不良'],
            'attendance' => ['required', 'in:参加できる,相談したい,参加が難しい'],
            'contactRequest' => ['required', 'in:不要,希望する'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '匿名練習報告の選択内容が正しくありません。',
                'errors' => $validator->errors(),
            ], 422);
        }

        $input = $validator->validated();

        $report = PracticeReport::create([
            'condition' => $input['condition'],
            'attendance' => $input['attendance'],
            'contact_request' => $input['contactRequest'],
            'status' => '未対応',
        ]);

        return response()->json((new PracticeReportResource($report))->resolve(), 201);
    }

    /**
     * 指定された匿名練習報告の対応状態だけを更新します。
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $report = PracticeReport::find($id);

        if ($report === null) {
            return response()->json([
                'message' => '指定した匿名練習報告が見つかりません。',
            ], 404);
        }

        $validator = Validator::make($request->json()->all(), [
            'status' => ['required', 'in:未対応,対応済み'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '対応状態は「未対応」または「対応済み」を指定してください。',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = $validator->validated()['status'];

        if ($report->status !== $status) {
            $report->update([
                'status' => $status,
                'status_changed_at' => now(),
            ]);

            $report->refresh();
        }

        return response()->json((new PracticeReportResource($report))->resolve());
    }

    /**
     * database.sqlite内の匿名練習報告だけをすべて削除し、削除件数を返します。
     */
    public function destroyAll(): JsonResponse
    {
        $deletedCount = PracticeReport::query()->delete();

        return response()->json([
            'deletedCount' => $deletedCount,
        ]);
    }
}
