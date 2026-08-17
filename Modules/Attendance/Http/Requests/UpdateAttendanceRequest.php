<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


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

    public function messages(): array
    {
    return [
        'check_in.date'           => __('The check_in must be a valid date'),

        'check_out.date'          => __('The check_out must be a valid date'),
        'check_out.after_or_equal'=> __('The check_out must be a date after or equal to check_in'),

        'status.string'           => __('The status must be a valid string'),
        'status.in'               => __('Invalid status, must be present, late, absent, on_leave, or holiday'),

        'notes.string'            => __('The notes must be a valid string'),
        'notes.max'               => __('The notes field may not be greater than 500 characters'),
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