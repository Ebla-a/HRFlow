<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'type' => ['required', 'in:check_in,check_out'],
        ];
    }

    public function messages(): array
    {
    return [
        'employee_id.required' => __('Employee ID is required'),
        'employee_id.exists'   => __('Selected employee does not exist'),
        'type.required'        => __('Attendance type is required'),
        'type.in'              => __('Invalid attendance type selected'),
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