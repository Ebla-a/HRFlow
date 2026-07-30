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
        'department_id' => 'required|integer|exists:departments,id',

        'job_title_id' => 'required|integer|exists:job_titles,id',

        'employee_number' => ['required','string', 'max:50',],

        'employment_type' => [ 'required', 'in:full_time,part_time,contract' ],

        'status' => [ 'required','in:active,on_leave,suspended,terminated' ],

        'hire_date' => ['required','date' ],
        'manager_id' => [ 'nullable','integer','exists:employees,id' ],

        'national_id' => [ 'nullable','string','max:50'],

        'phone' => 'nullable|string|max:20',

        'address' => 'nullable|string',

        'birth_date' => 'nullable|date',

        'gender' => 'nullable|in:male,female',
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