<?php

namespace Modules\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Employee\Http\Resources\V1\EmployeeDocumentResource;


class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->whenLoaded('user', fn () => $this->user->email),
            'phone' => $this->phone,
            'national_id' => $this->national_id,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'age' => $this->age,
            'gender' => $this->gender?->value,
            'address' => $this->address,
            'employment_type' => $this->employment_type?->value,
            'status' => $this->status?->value,
            'hire_date' => $this->hire_date?->format('Y-m-d'),
            'years_of_service' => $this->years_of_service,
            'termination_date' => $this->termination_date?->format('Y-m-d'),
            'termination_reason' => $this->termination_reason,
            'department' => $this->whenLoaded('department', fn () => [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ]),
            'job_title' => $this->whenLoaded('jobTitle', fn () => [
                'id' => $this->jobTitle->id,
                'title' => $this->jobTitle->title,
            ]),
            'manager' => $this->whenLoaded('manager', fn () => [
                'id' => $this->manager->id,
                'full_name' => $this->manager->full_name,
            ]),
            'documents' => EmployeeDocumentResource::collection($this->whenLoaded('documents')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}