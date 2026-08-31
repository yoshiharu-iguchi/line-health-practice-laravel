<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PracticeReportResource extends JsonResource
{
    /**
     * APIで返してよい匿名練習用の項目だけを選びます。
     *
     * @return array<string, int|string|null>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'condition' => $this->condition,
            'attendance' => $this->attendance,
            'contactRequest' => $this->contact_request,
            'status' => $this->status,
            'createdAt' => $this->created_at?->toIso8601String(),
            'statusChangedAt' => $this->status_changed_at?->toIso8601String(),
        ];
    }
}
