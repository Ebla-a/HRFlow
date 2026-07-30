<?php

namespace Modules\Employee\App\Http\Resources\V1;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
{
   return [
    'id' => $this->id,
    'employee_number' => $this->employee_number,

    'department_id' => $this->department_id,
    'job_title_id' => $this->job_title_id,
    'manager_id' => $this->manager_id,
    'manager' => [
    'id' => $this->manager?->id,
    'name' => $this->manager?->user?->name,
],
    'user' => [
    'id' => $this->user?->id,
    'name' => $this->user?->name,
    'email' => $this->user?->email,
],

'department' => [
    'id' => $this->department?->id,
    'name' => $this->department?->name,
],

'job_title' => [
    'id' => $this->jobTitle?->id,
    'name' => $this->jobTitle?->name,
],

    'employment_type' => $this->employment_type,
    'status' => $this->status,
    'hire_date' => $this->hire_date?->format('Y-m-d'),

    'national_id' => $this->national_id,
    'phone' => $this->phone,
    'address' => $this->address,
    'birth_date' => $this->birth_date?->format('Y-m-d'),
    'gender' => $this->gender,

    'termination_date' => $this->termination_date?->format('Y-m-d'),
    'termination_reason' => $this->termination_reason,
];
}
}
