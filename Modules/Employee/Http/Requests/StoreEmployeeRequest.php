<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\Employee\App\Enums\EmploymentType;
use Modules\Employee\App\Enums\Gender;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


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
            'gender' => ['required', Rule::in(Gender::values())],
        ];
    }


    public function messages(): array
    {
    return [
        'email.required'            => __('Email is required'),
        'email.email'               => __('The email must be a valid email address'),
        'email.max'                 => __('The email field may not be greater than 255 characters'),
        'email.unique'              => __('The email has already been taken'),

        'first_name.required'       => __('First name is required'),
        'first_name.string'         => __('First name must be a valid string'),
        'first_name.max'            => __('First name field may not be greater than 100 characters'),

        'last_name.required'        => __('Last name is required'),
        'last_name.string'          => __('Last name must be a valid string'),
        'last_name.max'             => __('Last name field may not be greater than 100 characters'),

        'department_id.required'    => __('Department is required'),
        'department_id.integer'     => __('Department ID must be an integer'),
        'department_id.exists'      => __('The selected department does not exist'),

        'job_title_id.required'     => __('Job title is required'),
        'job_title_id.integer'      => __('Job title ID must be an integer'),
        'job_title_id.exists'       => __('The selected job title does not exist'),

        'employee_number.required'  => __('Employee number is required'),
        'employee_number.string'    => __('Employee number must be a valid string'),
        'employee_number.max'       => __('Employee number field may not be greater than 50 characters'),
        'employee_number.unique'    => __('The employee number has already been taken'),

        'employment_type.required'  => __('Employment type is required'),
        'employment_type.in'        => __('Selected employment type is invalid'),

        'status.required'           => __('Status is required'),
        'status.in'                  => __('Selected status is invalid'),

        'hire_date.required'        => __('Hire date is required'),
        'hire_date.date'            => __('Hire date must be a valid date'),

        'manager_id.integer'        => __('Manager ID must be an integer'),
        'manager_id.exists'         => __('The selected manager does not exist'),

        'national_id.required'      => __('National ID is required'),
        'national_id.string'        => __('National ID must be a valid string'),
        'national_id.max'           => __('National ID field may not be greater than 50 characters'),
        'national_id.unique'        => __('The national ID has already been taken'),

        'phone.string'              => __('Phone number must be a valid string'),
        'phone.max'                 => __('Phone number field may not be greater than 30 characters'),

        'address.string'            => __('Address must be a valid string'),

        'birth_date.required'       => __('Birth date is required'),
        'birth_date.date'           => __('Birth date must be a valid date'),

        'gender.required'           => __('Gender is required'),
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