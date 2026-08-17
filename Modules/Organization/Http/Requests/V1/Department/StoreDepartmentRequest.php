<?php

namespace Modules\Organization\Http\Requests\V1\Department;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Organization\Entities\Department;
use Modules\Organization\Rules\PreventCircularDepartmentReference;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreDepartmentRequest extends FormRequest
{


  /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
  public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $departmentId = $this->route('department')?->id ?? $this->route('id');


      return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:departments,name',
            ],

            'code' => [
                'required',
                'string',
                'max:20',
                'unique:departments,code',
            ],

            'parent_id' => [
                'nullable',
                'integer',
                'exists:departments,id',new PreventCircularDepartmentReference($departmentId),
            ],

            'manager_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];

    }

public function messages(): array
{
    return [
        'name.required'      => __('Name is required'),
        'name.string'        => __('Name must be a valid string'),
        'name.max'           => __('Name field may not be greater than 255 characters'),
        'name.unique'        => __('The name has already been taken'),

        'code.required'      => __('Code is required'),
        'code.string'        => __('Code must be a valid string'),
        'code.max'           => __('Code field may not be greater than 20 characters'),
        'code.unique'        => __('The code has already been taken'),

        'parent_id.integer'  => __('Parent department ID must be an integer'),
        'parent_id.exists'   => __('The selected parent department does not exist'),

        'manager_id.integer' => __('Manager ID must be an integer'),
        'manager_id.exists'  => __('The selected manager does not exist'),

        'is_active.boolean'  => __('Is active field must be true or false'),
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
