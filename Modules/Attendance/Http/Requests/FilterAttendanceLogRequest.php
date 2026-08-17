<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class FilterAttendanceLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
    return [
        'employee_id' => [
            'nullable',
            'exists:employees,id'
        ],

        'type' => [
            'nullable',
            'in:check_in,check_out'
        ],

        'result' => [
            'nullable',
            'in:success,failed'
        ],

        'from_date' => [
            'nullable',
            'date'
        ],

        'to_date' => [
            'nullable',
            'date',
            'after_or_equal:from_date'
        ],

        'per_page' => [
            'nullable',
            'integer',
            'min:1',
            'max:100'
        ],
    ];
}

    public function messages(): array
    {
    return [
        'employee_id.exists'     => __('Selected employee does not exist'),

        'type.in'                => __('Invalid type, must be check_in or check_out'),

        'result.in'              => __('Invalid result, must be success or failed'),

        'from_date.date'         => __('The from_date must be a valid date'),

        'to_date.date'           => __('The to_date must be a valid date'),
        'to_date.after_or_equal' => __('The to_date must be a date after or equal to from_date'),

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