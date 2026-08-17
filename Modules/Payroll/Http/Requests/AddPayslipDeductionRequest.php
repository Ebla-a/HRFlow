<?php

namespace Modules\Payroll\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

final class AddPayslipDeductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
    return [
        'type.required'        => __('Type is required'),
        'type.string'          => __('Type must be a valid string'),

        'amount.required'      => __('Amount is required'),
        'amount.numeric'       => __('Amount must be a number'),
        'amount.min'           => __('Amount must be at least 0.01'),

        'description.required' => __('Description is required'),
        'description.string'   => __('Description must be a valid string'),
        'description.max'      => __('Description field may not be greater than 255 characters'),
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