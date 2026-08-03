<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}