<?php

namespace Modules\Payroll\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Payroll\Http\Resources\V1\PayslipResource;

class PayrollRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'period' => [
                'month' => $this->month,
                'year' => $this->year,
                'formatted' => sprintf('%02d/%d', $this->month, $this->year),
            ],
            'status' => $this->status,
            'notes' => $this->notes,
            'audit' => [
                'processed_by' => $this->whenLoaded('processedBy', fn () => [
                    'id' => $this->processedBy->id,
                    'name' => $this->processedBy->name,
                ]),
                'processed_at' => $this->processed_at?->toIso8601String(),
                'finalized_by' => $this->whenLoaded('finalizedBy', fn () => [
                    'id' => $this->finalizedBy->id,
                    'name' => $this->finalizedBy->name,
                ]),
                'finalized_at' => $this->finalized_at?->toIso8601String(),
            ],
            'payslips' => PayslipResource::collection($this->whenLoaded('payslips')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}