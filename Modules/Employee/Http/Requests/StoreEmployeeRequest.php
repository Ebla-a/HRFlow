<?php

namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\Employee\App\Enums\EmploymentType;
use Modules\Employee\App\Enums\Gender;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255|unique:users,email',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'department_id' => 'required|integer|exists:departments,id',
            'job_title_id' => 'required|integer|exists:job_titles,id',
            'employee_number' => 'required|string|max:50|unique:employees,employee_number',
            'employment_type' => ['required', Rule::in(EmploymentType::values())],
            'status' => ['required', Rule::in(EmployeeStatus::values())],
            'hire_date' => 'required|date',
            'manager_id' => 'nullable|integer|exists:employees,id',
            'national_id' => 'required|string|max:50|unique:employees,national_id',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'birth_date' => 'required|date',
            'gender' => ['nullable', Rule::in(Gender::values())],
        ];
    }
}