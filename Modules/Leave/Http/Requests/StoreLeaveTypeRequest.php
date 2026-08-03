<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:leave_types,name',
            ],

            'annual_days' => [
                'required',
                'integer',
                'min:1',
            ],

            'is_paid' => [
                'required',
                'boolean',
            ],

            'requires_attachment' => [
                'required',
                'boolean',
            ],
        ];
    }
}
 