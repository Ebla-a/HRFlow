<?php

namespace Modules\Organization\Http\Requests\V1\JobTitle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Organization\Enums\JobTitleGrade;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreJobTitleRequest extends FormRequest
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
         return [

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('job_titles', 'title->en')
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

    public function messages(): array
{
    return [
        'department_id.required' => __('Department ID is required'),
        'department_id.integer'  => __('Department ID must be an integer'),
        'department_id.exists'   => __('The selected department does not exist'),

        'title.required'         => __('Title is required'),
        'title.string'           => __('Title must be a valid string'),
        'title.max'              => __('Title field may not be greater than 255 characters'),
        'title.unique'           => __('The title has already been taken for this department'),

        'description.string'     => __('Description must be a valid string'),
        'description.max'        => __('Description field may not be greater than 1000 characters'),

        'grade.enum'             => __('The selected grade is invalid'),

        'is_active.boolean'      => __('Is active field must be true or false'),
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


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => __('Validation errors'),
            'errors'  => $validator->errors()->toArray(),
        ], 422));
    }
}
