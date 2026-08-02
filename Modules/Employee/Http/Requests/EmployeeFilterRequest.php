<?php

namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\App\Enums\EmployeeStatus;

class EmployeeFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer|exists:departments,id',
            'sort_by' => 'nullable|in:hire_date,age,first_name,department_id',
            'direction' => 'nullable|in:asc,desc',
            'status' => ['nullable', Rule::in(EmployeeStatus::values())],
        ];
    }
}