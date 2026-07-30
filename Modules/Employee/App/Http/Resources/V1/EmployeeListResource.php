<?php

namespace Modules\Employee\App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeListResource extends JsonResource
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

        'name' => $this->user?->name,

        'department' => [
            'id' => $this->department?->id,
            'name' => $this->department?->name,
        ],

        'job_title' => [
            'id' => $this->jobTitle?->id,
            'name' => $this->jobTitle?->name,
        ],

        'manager' => [
            'id' => $this->manager?->id,
            'name' => $this->manager?->user?->name,
        ],

        'employment_type' => $this->employment_type,
        'status' => $this->status,
        'hire_date' => $this->hire_date?->format('Y-m-d'),
    ];
}}
