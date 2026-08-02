<?php

namespace Modules\Performance\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Performance\Rules\Filter;

class ReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'performance_cycle_id' => ['required', 'exists:performance_cycles,id'],
            'reviewer_id' => ['required', 'exists:employees,id'],
            'score' => ['required', 'integer', 'between:1,5'],
            'comments' => ['required', 'string', 'max:255',new Filter()],
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
            'employee_id.required' => 'The employee is required.',
            'performance_cycle_id.required' => 'The performance cycle is required.',
            'reviewer_id.required' => 'The reviewer is required.',
            'score.required' => 'The score is required.',
            'score.integer' => 'The score must be an integer.',
            'score.between' => 'The score must be between 1 and 5.',
            'comments.required' => 'The comments are required.',
            'comments.string' => 'The comments must be a string.',
            'comments.max' => 'The comments may not exceed 255 characters.',
            'comments.filter'=>'The comments contain inappropriate words.',
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