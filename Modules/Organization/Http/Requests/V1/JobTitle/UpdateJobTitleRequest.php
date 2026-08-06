<?php

namespace Modules\Organization\Http\Requests\V1\JobTitle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Organization\Entities\JobTitle;
use Modules\Organization\Enums\JobTitleGrade;

class UpdateJobTitleRequest extends FormRequest
{
/**
 * Summary of prepareForValidation
 * @return void
 */
protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->has('title') && is_string($this->title)) {
            $mergeData['title'] = ucwords(strtolower(trim($this->title)));
        }

        if ($this->has('description')) {
            $mergeData['description'] = $this->filled('description') ? trim($this->description) : null;
        }

        if ($this->has('is_active')) {
            $mergeData['is_active'] = filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {


$routeParam = $this->route('id') ?? $this->route('job_title') ?? $this->route('jobTitle');
$jobTitle = $routeParam instanceof JobTitle
            ? $routeParam
            : JobTitle::find($routeParam);

$departmentId = $this->input('department_id') ?? $jobTitle?->department_id;



        return [

            'department_id' => ['sometimes',
                'integer',
                'exists:departments,id',
            ],

    'title' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('job_titles', 'title')
                    ->where(fn ($query) => $query->where('department_id', $departmentId))
                    ->ignore($jobTitle?->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'grade' => [
                'nullable',
                new Enum(JobTitleGrade::class),
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
