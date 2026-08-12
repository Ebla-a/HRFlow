<?php

namespace Modules\Organization\Http\Requests\V1\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Organization\Entities\Department;
use Modules\Organization\Rules\PreventCircularDepartmentReference;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $departmentId = $this->route('department')?->id ?? $this->route('id');


        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('departments', 'name')->ignore($departmentId)],
            'code' => ['sometimes', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($departmentId)],
            'description' => ['nullable', 'string'],
            //restrict partent_id = department_id to avoid circular reference
            'parent_id' => ['nullable', 'exists:departments,id',new PreventCircularDepartmentReference($departmentId)  ],

            'manager_id' => ['nullable', 'exists:employees,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }



}


