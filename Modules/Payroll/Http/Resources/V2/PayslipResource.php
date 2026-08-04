<?php

namespace Modules\Payroll\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Payroll\Http\Resources\V2\PayslipDeductionResource;

class PayslipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => $this->whenLoaded('employee', fn () => [
                'id' => $this->employee->id,
                'full_name' => $this->employee->full_name,
                'employee_number' => $this->employee->employee_number,
            ]),
            'salary_breakdown' => [
                'basic_salary' => (float) $this->basic_salary,
                'housing_allowance' => (float) $this->housing_allowance,
                'transport_allowance' => (float) $this->transport_allowance,
                'other_allowance' => (float) $this->other_allowance,
                'gross_salary' => (float) $this->gross_salary,
            ],
            'deductions_breakdown' => [
                'unpaid_leave_days' => $this->unpaid_leave_days,
                'unpaid_leave_deduction' => (float) $this->unpaid_leave_deduction,
                'manual_deductions' => (float) $this->deductions,
                'items' => PayslipDeductionResource::collection($this->whenLoaded('deductions')),
            ],
            'net_salary' => (float) $this->net_salary,
        ];
    }
}