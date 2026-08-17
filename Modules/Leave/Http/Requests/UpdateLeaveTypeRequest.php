<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;



class UpdateLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $leaveType = $this->route('leaveType');

        return [

            'name' => [
                'required',
                'string',
                'max:255',
                'unique:leave_types,name,' . $leaveType->id,
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

    public function messages(): array
{
    return [
        'name.required'                => __('Name is required'),
        'name.string'                  => __('Name must be a valid string'),
        'name.max'                     => __('Name field may not be greater than 255 characters'),
        'name.unique'                  => __('The name has already been taken'),

        'annual_days.required'         => __('Annual days is required'),
        'annual_days.integer'          => __('Annual days must be an integer'),
        'annual_days.min'              => __('Annual days must be at least 1'),

        'is_paid.required'             => __('Is paid field is required'),
        'is_paid.boolean'              => __('Is paid field must be true or false'),

        'requires_attachment.required' => __('Requires attachment field is required'),
        'requires_attachment.boolean'  => __('Requires attachment field must be true or false'),
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
 