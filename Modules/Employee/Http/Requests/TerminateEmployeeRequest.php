<?php

namespace Modules\Employee\App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class TerminateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'termination_reason' => 'required|string|max:500',
        ];
    }
}