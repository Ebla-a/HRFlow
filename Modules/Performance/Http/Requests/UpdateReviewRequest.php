<?php

namespace Modules\Performance\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Performance\Rules\Filter;

class UpdateReviewRequest extends FormRequest
{

  public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'score'    => ['required', 'integer', 'between:1,5'],
            'comments' => ['required', 'string', 'max:255', new Filter()],
        ];
    }

    public function messages(): array
    {
        return [
            'score.required'    => 'The score is required.',
            'score.integer'     => 'The score must be an integer.',
            'score.between'     => 'The score must be between 1 and 5.',
            'comments.required' => 'The comments are required.',
            'comments.string'   => 'The comments must be a string.',
            'comments.max'      => 'The comments may not exceed 255 characters.',
            'comments.filter'   => 'The comments contain inappropriate words.',
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