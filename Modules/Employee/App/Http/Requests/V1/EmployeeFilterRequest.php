<?php

namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Summary of rules
     * @return array{department_id: string, direction: string, search: string, sort_by: string}
     */
    public function rules(): array
    {
        return [
            'search'        => 'sometimes|nullable|string|max:255',
            'department_id' => 'sometimes|nullable|integer|exists:departments,id',
            'sort_by'       => 'sometimes|nullable|in:hire_date,age,name,department',
            'direction'     => 'sometimes|nullable|in:asc,desc',
            'status' => 'sometimes|nullable|in:active,on_leave,suspended,terminated',
        ];
    }
}