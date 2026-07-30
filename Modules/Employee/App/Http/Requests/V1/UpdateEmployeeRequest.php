<?php
namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Summary of authorize
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Summary of rules
     * @return array{address: string, birth_date: string, department_id: string, employee_number: array<string|\Illuminate\Validation\Rules\Unique>, employment_type: string, gender: string, hire_date: string, job_title_id: string, manager_id: array<string|\Illuminate\Validation\Rules\NotIn>, national_id: array<string|\Illuminate\Validation\Rules\Unique>, phone: string, status: string}
     */
    public function rules(): array
    {
        // Get the employee instance from the route (Route Model Binding)
        $employee = $this->route('employee');
        $employeeId = $employee ? $employee->id : $this->route('id');

        return [
            'department_id'   => 'sometimes|integer|exists:departments,id',
            'job_title_id'    => 'sometimes|integer|exists:job_titles,id',
            'manager_id'      => [
                'nullable',
                'integer',
                'exists:employees,id',
                Rule::notIn([$employeeId]), // Prevent assigning the employee as their own manager
            ],
            'employee_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_number')->ignore($employeeId)
            ],
            'national_id'     => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('employees', 'national_id')->ignore($employeeId)
            ],
            'employment_type' => 'sometimes|in:full_time,part_time,contract',
            'status'          => 'sometimes|in:active,on_leave,suspended,terminated',
            'hire_date'       => 'sometimes|date',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'birth_date'      => 'nullable|date',
            'gender'          => 'nullable|in:male,female',
        ];
    }
}
