<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}