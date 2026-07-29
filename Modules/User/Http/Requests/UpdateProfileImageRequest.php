<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdateProfileImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'    => 'integer|required|exists:users,id',
            'avatar_url' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048', 
        ];
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

    public function messages(): array
    {
        return [
            'id.required'    => 'User ID is required.',
            'id.integer'     => 'User ID must be a valid number.',
            'id.exists'      => 'Selected user does not exist.',
            'image.required' => 'Please select a profile image to upload.',
            'image.file'     => 'The uploaded item must be a valid file.',
            'image.image'    => 'The uploaded file must be an image.',
            'image.mimes'    => 'Only JPEG, PNG, JPG, and WEBP image formats are allowed.',
            'image.max'      => 'The profile image size must not exceed 2MB.',
        ];
    }

protected function failedValidation(Validator $validator): void 
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
