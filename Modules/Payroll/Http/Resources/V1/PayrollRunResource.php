<?php

namespace Modules\Payroll\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Employee\Http\Resources\V1\EmployeeResource;
use Modules\Payroll\Http\Resources\V2\PayslipDeductionResource;

class PayrollRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'payroll_run_id' => $this->payroll_run_id,
            'basic_salary' => $this->basic_salary,
            'total_allowances' => $this->total_allowances,
            'total_deductions' => $this->total_deductions,
            'net_salary' => $this->net_salary,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'deductions' => PayslipDeductionResource::collection($this->whenLoaded('deductions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,

        ];
    }
}