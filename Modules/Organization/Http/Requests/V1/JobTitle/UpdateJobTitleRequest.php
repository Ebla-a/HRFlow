<?php

namespace Modules\Organization\Http\Requests\V1\JobTitle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Organization\Enums\JobTitleGrade;

class UpdateJobTitleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       $jobTitleId = $this->route('job_title')?->id ?? $this->route('id');

       return [
            'department_id' => [
                'integer',
                'exists:departments,id',
            ],

      'title' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('job_titles')
                    ->ignore($jobTitleId)
                    ->where(fn ($query) => $query->where(
                        'department_id',
                        $this->department_id
                    )),
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
