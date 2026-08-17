<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\App\Enums\EmployeeStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class EmployeeFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer|exists:departments,id',
            'sort_by' => 'nullable|in:hire_date,age,first_name,department_id',
            'direction' => 'nullable|in:asc,desc',
            'status' => ['nullable', Rule::in(EmployeeStatus::values())],
        ];
    }

    public function messages(): array
    {
    return [
        'search.string'         => __('The search parameter must be a valid string'),
        'search.max'            => __('The search parameter may not be greater than 255 characters'),

        'department_id.integer' => __('The department ID must be an integer'),
        'department_id.exists'  => __('The selected department does not exist'),

        'sort_by.in'            => __('Invalid sort_by field, must be hire_date, age, first_name, or department_id'),

        'direction.in'          => __('Invalid direction, must be asc or desc'),

        'status.in'             => __('Selected status is invalid'),
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