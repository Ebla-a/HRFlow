<?php

namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\Employee\App\Enums\EmploymentType;
use Modules\Employee\App\Enums\Gender;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $employeeId = $employee ? $employee->id : $this->route('id');

        return [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'department_id' => 'sometimes|integer|exists:departments,id',
            'job_title_id' => 'sometimes|integer|exists:job_titles,id',
            'manager_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
                Rule::notIn([$employeeId]),
            ],
            'employee_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_number')->ignore($employeeId)
            ],
            'national_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('employees', 'national_id')->ignore($employeeId)
            ],
            'employment_type' => ['sometimes', Rule::in(EmploymentType::values())],
            'status' => ['sometimes', Rule::in(EmployeeStatus::values())],
            'hire_date' => 'sometimes|date',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => ['nullable', Rule::in(Gender::values())],
        ];
    }
}