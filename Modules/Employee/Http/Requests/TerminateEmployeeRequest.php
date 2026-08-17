<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;



class TerminateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'termination_reason' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
    return [
        'termination_reason.required' => __('Termination reason is required'),
        'termination_reason.string'   => __('Termination reason must be a valid string'),
        'termination_reason.max'      => __('Termination reason may not be greater than 500 characters'),
    ];
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