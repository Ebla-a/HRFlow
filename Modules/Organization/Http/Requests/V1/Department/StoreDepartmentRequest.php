<?php

namespace Modules\Organization\Http\Requests\V1\Department;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Organization\Entities\Department;
use Modules\Organization\Rules\PreventCircularDepartmentReference;

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




}
