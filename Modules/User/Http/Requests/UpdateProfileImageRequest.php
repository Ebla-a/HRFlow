<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update avatar');
    }

    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp,gif',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Please upload a profile image.',
            'avatar.image'    => 'The uploaded file must be an image.',
            'avatar.mimes'    => 'Only JPEG, PNG, JPG, WEBP, and GIF formats are allowed.',
            'avatar.max'      => 'Image size must not exceed 2MB.',
        ];
    }
}
