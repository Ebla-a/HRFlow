<?php

namespace Modules\Auth\Http\Requests;

use Modules\Auth\App\DTOs\ResetPasswordDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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

    public function toDTO(): ResetPasswordDTO
   {
    return new ResetPasswordDTO(
        email: $this->validated('email'),
        token: $this->validated('token'),
        password: $this->validated('password'),
    );
   }
}
 