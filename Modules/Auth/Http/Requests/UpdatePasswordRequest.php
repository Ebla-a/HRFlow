<?php

namespace Modules\Auth\Http\Requests;

use Modules\Auth\App\DTOs\ChangePasswordDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property-read string $current_password
 * @property-read string $password
 */
class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required', 
                'string',
                'current_password',
             ],
            'password' => [
                'required', 
                'string', 
                Password::defaults(),
                'confirmed',
                'different:current_password',
             ],
        ];
    }

    public function toDTO(): ChangePasswordDTO
   { 
    return new ChangePasswordDTO(
        currentPassword: $this->validated('current_password'),
        password: $this->validated('password'),
    );
   }
}
 