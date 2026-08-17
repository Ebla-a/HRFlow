<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\Employee\App\Enums\EmploymentType;
use Modules\Employee\App\Enums\Gender;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;



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

    public function messages(): array
    {
    return [
        'first_name.string'         => __('First name must be a valid string'),
        'first_name.max'            => __('First name field may not be greater than 100 characters'),

        'last_name.string'          => __('Last name must be a valid string'),
        'last_name.max'             => __('Last name field may not be greater than 100 characters'),

        'department_id.integer'     => __('Department ID must be an integer'),
        'department_id.exists'      => __('The selected department does not exist'),

        'job_title_id.integer'      => __('Job title ID must be an integer'),
        'job_title_id.exists'       => __('The selected job title does not exist'),

        'manager_id.integer'        => __('Manager ID must be an integer'),
        'manager_id.exists'         => __('The selected manager does not exist'),
        'manager_id.not_in'         => __('An employee cannot be their own manager'),

        'employee_number.string'    => __('Employee number must be a valid string'),
        'employee_number.max'       => __('Employee number field may not be greater than 50 characters'),
        'employee_number.unique'    => __('The employee number has already been taken'),

        'national_id.string'        => __('National ID must be a valid string'),
        'national_id.max'           => __('National ID field may not be greater than 50 characters'),
        'national_id.unique'        => __('The national ID has already been taken'),

        'employment_type.in'        => __('Selected employment type is invalid'),

        'status.in'                 => __('Selected status is invalid'),

        'hire_date.date'            => __('Hire date must be a valid date'),

        'phone.string'              => __('Phone number must be a valid string'),
        'phone.max'                 => __('Phone number field may not be greater than 30 characters'),

        'address.string'            => __('Address must be a valid string'),

        'birth_date.date'           => __('Birth date must be a valid date'),

        'gender.in'                 => __('Selected gender is invalid'),
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