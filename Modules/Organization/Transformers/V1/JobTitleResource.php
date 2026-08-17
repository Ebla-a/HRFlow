<?php

namespace Modules\Organization\Transformers\V1;
use Illuminate\Http\Resources\Json\JsonResource;

class JobTitleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
     return [
            'id' => $this->id,
            'title' => $this->title,
            'grade' => $this->grade ? __($this->grade->value) : null,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'department_name' => $this->department->name ?? null,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at'=> $this->updated_at?->format('Y-m-d'),

            'employees' => $this->whenLoaded('employees', function () {
                return $this->employees->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'first_name' => $employee->first_name,
                        'last_name' => $employee->last_name,
                        'email' => $employee->user->email,
                    ];
                });
            }),
            
        ];    }
}
