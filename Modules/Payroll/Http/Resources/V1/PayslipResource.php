<?php

namespace Modules\Payroll\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayslipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'basic_salary' => (float) $this->basic_salary,
            'gross_salary' => (float) $this->gross_salary,
            'deductions' => (float) $this->deductions,
            'net_salary' => (float) $this->net_salary,
        ];
    }
}