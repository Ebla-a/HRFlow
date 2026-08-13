<?php

namespace Modules\Performance\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Performance\Rules\Filter;
use Modules\Performance\Rules\MinimumDaysAfter;

class CycleRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255', new Filter()],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date'   => ['required', 'date', new MinimumDaysAfter()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'The cycle name is required.',
            'name.string'                 => 'The cycle name must be a string.',
            'name.max'                    => 'The cycle name may not exceed 255 characters.',
            'name.filter'                 => 'The name contains inappropriate words.',
            'start_date.required'         => 'The start date is required.',
            'start_date.date'             => 'The start date must be a valid date.',
            'start_date.after'            => 'The start date must be a date after today.',
            'end_date.required'           => 'The end date is required.',
            'end_date.date'               => 'The end date must be a valid date.',
            'end_date.minimum_days_after' => 'The end date must be at least 3 days after the start date.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors()->toArray(),
        ], 422));
    }

}