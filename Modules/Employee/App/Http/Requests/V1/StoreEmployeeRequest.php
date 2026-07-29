<?php

namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
  public function rules(): array
{
    return [
        'department_id' => 'required|integer',
        'job_title_id' => 'required|integer',
        'employee_number' => 'required|string',

        'employment_type' => 'required|in:full_time,part_time,contract',
        'status' => 'required|in:active,on_leave,suspended,terminated',

        'hire_date' => 'required|date',
    ];
}
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
{
    return true;
}
}