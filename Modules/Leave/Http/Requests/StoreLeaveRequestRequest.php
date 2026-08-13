<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Modules\Leave\Entities\LeaveType;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (auth('sanctum')->check() && auth('sanctum')->user()->employee) {
            $this->merge([
                'employee_id' => auth('sanctum')->user()->employee->id,
            ]);
        }
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

        if ($this->filled(['leave_type_id', 'start_date', 'end_date'])) {
            $leaveType = LeaveType::find($this->leave_type_id);

            if ($leaveType && strtolower($leaveType->name) === 'sick') {
                $days = Carbon::parse($this->start_date)
                    ->diffInDays(Carbon::parse($this->end_date)) + 1;

                if ($days > 2) {
                    $rules['attachment'][0] = 'required';
                }
            }
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled(['leave_type_id', 'start_date', 'end_date'])) {
                return;
            }

            $leaveType = LeaveType::find($this->leave_type_id);

            if (!$leaveType) {
                return;
            }

            if (strtolower($leaveType->name) === 'sick') {
                $days = Carbon::parse($this->start_date)
                    ->diffInDays(Carbon::parse($this->end_date)) + 1;

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