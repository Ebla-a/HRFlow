<?php

namespace Modules\Performance\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Performance\Rules\Filter;

class ReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id'          => ['required', 'exists:employees,id'],
            'performance_cycle_id' => ['required', 'exists:performance_cycles,id'],
            'score'                => ['required', 'integer', 'between:1,5'],
            'comments'             => ['required', 'string', 'max:255', new Filter()],
        ];
    }

    protected function prepareForValidation(): void
    {
       
        if (auth('sancutm')->check() && auth('sanctum')->user()->employee) {
            $this->merge([
                'reviewer_id' => auth('sanctum')->user()->employee->id,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'employee_id.required'          => 'The employee is required.',
            'employee_id.exists'            => 'The selected employee does not exist.',
            'performance_cycle_id.required' => 'The performance cycle is required.',
            'performance_cycle_id.exists'   => 'The selected cycle does not exist.',
            'score.required'                => 'The score is required.',
            'score.integer'                 => 'The score must be an integer.',
            'score.between'                 => 'The score must be between 1 and 5.',
            'comments.required'             => 'The comments are required.',
            'comments.string'               => 'The comments must be a string.',
            'comments.max'                  => 'The comments may not exceed 255 characters.',
            'comments.filter'               => 'The comments contain inappropriate words.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors()->toArray(),
        ], 422));
    }

    public function authorize(): bool
    {
        return true;
    }
}