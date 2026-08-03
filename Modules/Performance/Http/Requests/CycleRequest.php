<?php

namespace Modules\Performance\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Performance\Rules\Filter;
use Modules\Performance\Rules\MinimumDaysAfter;
use Override;

class CycleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', new Filter()],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' =>['required', 'date', new MinimumDaysAfter()],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The cycle name is required.',
            'name.string' => 'The cycle name must be a string.',
            'name.max' => 'The cycle name may not exceed 255 characters.',
            'name.filter'=>'The name contains inappropriate words.',
            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'start_date.after' => 'The start date must be a date after today.',
            'end_date.required' => 'The end date is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.minimum_days_after' => 'The end date must be at least 3 days after the start date.',

        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        
        $errors = $validator->errors()->toArray();
        $jsonResponse = Controller::error(
            'Validation errors',
            422,
            $errors
        );
        throw new HttpResponseException($jsonResponse);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
