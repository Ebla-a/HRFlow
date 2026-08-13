<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Requests\V1\Department;

use Illuminate\Foundation\Http\FormRequest;

class AssignManagerRequest extends FormRequest
{
    /**
     * Authorization is handled by the route middleware
     * and DepartmentPolicy.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'manager_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'manager_id.required' =>
                'You must select a manager to assign to the department.',

            'manager_id.integer' =>
                'The manager id must be a valid integer.',

            'manager_id.exists' =>
                'The selected manager does not exist in the system.',
        ];
    }
}