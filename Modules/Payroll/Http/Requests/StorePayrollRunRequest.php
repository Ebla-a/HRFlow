<?php

namespace Modules\Payroll\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


final class StorePayrollRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
    return [
        'month.required' => __('Month is required'),
        'month.integer'  => __('Month must be an integer'),
        'month.between'  => __('Month must be between 1 and 12'),

        'year.required'  => __('Year is required'),
        'year.integer'   => __('Year must be an integer'),
        'year.min'       => __('Year must be at least 2020'),

        'notes.string'   => __('Notes must be a valid string'),
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