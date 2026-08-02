<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Leave\Entities\LeaveType;

class StoreLeaveRequestRequest extends FormRequest
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
                'exists:employees,id'
            ],

            'leave_type_id' => [
                'required',
                'exists:leave_types,id'
            ],

            'start_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date'
            ],

            'reason' => [
                'nullable',
                'string',
                'max:500'
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }
}
 