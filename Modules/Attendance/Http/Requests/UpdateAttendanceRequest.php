<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // مخصص لـ HR Admin
    }

    public function rules(): array
    {
        return [
            'check_in' => ['nullable', 'date'],
            'check_out' => ['nullable', 'date', 'after_or_equal:check_in'],
            'status' => ['nullable', 'string', 'in:present,late,absent,on_leave,holiday'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}