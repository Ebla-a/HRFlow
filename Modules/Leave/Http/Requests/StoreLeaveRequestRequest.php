<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
   {
       $rules = [
        'employee_id' => ['required', 'exists:employees,id'],
        'leave_type_id' => ['required', 'exists:leave_types,id'],
        'start_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        'reason' => ['nullable', 'string'],
        'attachment' => [
            'nullable',
            'file',
            'mimes:pdf,jpg,jpeg,png',
            'max:2048',
        ],
    ];

    $leaveType = \Modules\Leave\Entities\LeaveType::find(
        $this->leave_type_id
    );

    if (
        $leaveType &&
        strtolower($leaveType->name) === 'sick'
    ) {

        $days = \Carbon\Carbon::parse($this->start_date)
            ->diffInDays(
                \Carbon\Carbon::parse($this->end_date)
            ) + 1;

        if ($days > 2) {

            $rules['attachment'][0] = 'required';
        }
    }
    return $rules;
}

    /**
     * Sick leave attachment validation
     */

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $leaveType = \Modules\Leave\Entities\LeaveType::find(
                $this->leave_type_id
            );

            if (!$leaveType) {
                return;
            }

            if ($leaveType->name === 'Sick') {
                $days = now()
                    ->parse($this->start_date)
                    ->diffInDays(
                        now()->parse($this->end_date)
                    ) + 1;

                if ($days > 2 && !$this->hasFile('attachment')) {
                    $validator->errors()->add(
                        'attachment',
                        'Attachment is required for sick leave longer than two days.'
                    );
                }
            }
        });
    }
}