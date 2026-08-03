<?php

namespace Modules\Auth\Http\Requests;

use Modules\Auth\App\DTOs\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $email
 * @property-read string $password
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
       public function rules(): array
{
    return [
        'email' => ['required'],
        'password' => ['required'],
    ];
}
    

    public function toDTO(): LoginDTO
   {
    return new LoginDTO(
        email: $this->validated('email'),
        password: $this->validated('password'),
    );
   }
}
 