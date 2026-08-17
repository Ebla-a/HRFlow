<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class FilterAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
    return [
        'employee_id' => ['nullable', 'exists:employees,id'],

        'status' => ['nullable', 'in:present,late,absent,on_leave,holiday'],

        'from_date' => ['nullable', 'date'],
        'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],

        'late' => ['nullable', 'boolean'],

        'sort_by' => ['nullable', 'in:arrival,late,notes'],

        'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
    ];
    }

    public function messages(): array
    {
    return [
        'employee_id.exists'     => __('Selected employee does not exist'),

        'status.in'              => __('Invalid status, must be present, late, absent, on_leave, or holiday'),

        'from_date.date'         => __('The from_date must be a valid date'),

        'to_date.date'           => __('The to_date must be a valid date'),
        'to_date.after_or_equal' => __('The to_date must be a date after or equal to from_date'),

        'late.boolean'           => __('The late field must be true or false'),

        'sort_by.in'             => __('Invalid sort_by field, must be arrival, late, or notes'),

        'per_page.integer'       => __('The per_page parameter must be an integer'),
        'per_page.min'           => __('The per_page parameter must be at least 1'),
        'per_page.max'           => __('The per_page parameter may not be greater than 100'),
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