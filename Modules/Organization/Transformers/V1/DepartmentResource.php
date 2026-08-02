<?php

namespace Modules\Organization\Transformers\V1;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Organization\Transformers\V1\JobTitleResource;

class DepartmentResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id ?? null,

            'children' => self::collection($this->whenLoaded('childrenRecursive')),
              'manager'=>$this->manager_name,
              'main_department_name'=>$this->main_department_name,

            'job_titles' => JobTitleResource::collection($this->whenLoaded('jobTitles', function () {
                return $this->jobTitles->map(function ($jobTitle) {
                 return[

                    'id' => $jobTitle->id,
                    'title' => $jobTitle->title,
                    'description' => $jobTitle->description,
                    'is_active' => $jobTitle->is_active,

                        ];
               });
            })),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),

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

        ];

        }
}
