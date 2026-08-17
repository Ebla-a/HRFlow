<?php

namespace Modules\Organization\Http\Requests\V1\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Organization\Entities\Department;
use Modules\Organization\Rules\PreventCircularDepartmentReference;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


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
            'parent_id' => ['nullable', 'exists:departments,id',new PreventCircularDepartmentReference($departmentId)  ],

            'manager_id' => ['nullable', 'exists:employees,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
{
    return [
        'name.string'         => __('Name must be a valid string'),
        'name.max'            => __('Name field may not be greater than 255 characters'),
        'name.unique'         => __('The name has already been taken'),

        'code.string'         => __('Code must be a valid string'),
        'code.max'            => __('Code field may not be greater than 20 characters'),
        'code.unique'         => __('The code has already been taken'),

        'description.string'  => __('Description must be a valid string'),

        'parent_id.exists'    => __('The selected parent department does not exist'),

        'manager_id.exists'   => __('The selected manager does not exist'),

        'is_active.boolean'   => __('Is active field must be true or false'),
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


            protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => __('Validation errors'),
            'errors'  => $validator->errors()->toArray(),
        ], 422));
    }



}


