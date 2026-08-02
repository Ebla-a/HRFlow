<?php

namespace Modules\Organization\Http\Requests\V1\Department;
use Illuminate\Foundation\Http\FormRequest;

class AssignManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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

    public function messages(): array
    {
        return [
            'manager_id.required' =>'you must select a manager to assign to the department.',
            'manager_id.exists'   => 'the selected manager does not exist in the system.',
        ];
    }
}
