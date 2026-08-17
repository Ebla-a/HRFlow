<?php

namespace Modules\Auth\Http\Requests;

use Modules\Auth\App\DTOs\ResetPasswordDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


/**
 * @property-read string $token
 * @property-read string $email
 * @property-read string $password
 */
class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => __('Token is required'),
            'token.string'       => __('Token must be a valid string'),

            'email.required'     => __('Email is required'),
            'email.email'        => __('The email must be a valid email address'),
            'email.exists'       => __('The selected email does not exist'),

            'password.required'  => __('Password is required'),
            'password.string'    => __('Password must be a valid string'),
            'password.confirmed' => __('Password confirmation does not match'),
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

    public function toDTO(): ResetPasswordDTO
    {
    return new ResetPasswordDTO(
        email: $this->validated('email'),
        token: $this->validated('token'),
        password: $this->validated('password'),
    );
    }
}
 