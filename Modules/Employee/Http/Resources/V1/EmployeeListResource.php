<?php

namespace Modules\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'full_name' => $this->full_name,
            'email' => $this->whenLoaded('user', fn () => $this->user->email),
            'department' => $this->whenLoaded('department', fn () => $this->department->name),
            'job_title' => $this->whenLoaded('jobTitle', fn () => $this->jobTitle->title),
            'status' => $this->status?->value,
            'employment_type' => $this->employment_type?->value,
            'hire_date' => $this->hire_date?->format('Y-m-d'),
        ];
    }
}