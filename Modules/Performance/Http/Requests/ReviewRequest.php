<?php

namespace Modules\Performance\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Performance\Rules\Filter;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'reviewer_id' => [
                'required',
                'exists:employees,id',
            ],

            'performance_cycle_id' => [
                'required',
                'exists:performance_cycles,id',
            ],

            'score' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'comments' => [
                'required',
                'string',
                'max:255',
                new Filter(),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (
            auth('sanctum')->check() &&
            auth('sanctum')->user()->employee
        ) {
            $this->merge([
                'reviewer_id' => auth('sanctum')->user()->employee->id,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => __('The employee is required.'),
            'employee_id.exists' => __('The selected employee does not exist.'),

            'performance_cycle_id.required' => __('The performance cycle is required.'),
            'performance_cycle_id.exists' => __('The selected cycle does not exist.'),

            'score.required' => __('The score is required.'),
            'score.integer' => __('The score must be an integer.'),
            'score.between' => __('The score must be between 1 and 5.'),

            'comments.required' => __('The comments are required.'),
            'comments.string' => __('The comments must be a string.'),
            'comments.max' => __('The comments may not exceed 255 characters.'),
            'comments.filter' => __('The comments contain inappropriate words.'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => __('Validation errors'),
                'errors' => $validator->errors()->toArray(),
            ], 422)
        );
    }
}