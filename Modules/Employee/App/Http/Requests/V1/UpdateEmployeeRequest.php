<?php

namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
   public function authorize(): bool
{
    return true;
}
/**
 * /
 * @return array{address: string, birth_date: string, department_id: string, employee_number: string, employment_type: string, gender: string, hire_date: string, job_title_id: string, manager_id: string, national_id: string, phone: string, status: string}
 */
public function rules(): array
{
    return [
        'department_id' => 'sometimes|integer',
        'job_title_id' => 'sometimes|integer',
        'employee_number' => 'sometimes|string',

        'employment_type' => 'sometimes|in:full_time,part_time,contract',
        'status' => 'sometimes|in:active,on_leave,suspended,terminated',

        'hire_date' => 'sometimes|date',

        'manager_id' => 'nullable|integer',
        'national_id' => 'nullable|string',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'birth_date' => 'nullable|date',
        'gender' => 'nullable|in:male,female',
    ];
}
}