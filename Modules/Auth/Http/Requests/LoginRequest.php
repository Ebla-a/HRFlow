<?php

namespace Modules\Auth\Http\Requests;

use Modules\Auth\App\DTOs\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    public function messages(): array
    {
    return [
        'email.required'    => __('Email is required'),
        'password.required' => __('Password is required'),
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
    

    public function toDTO(): LoginDTO
    {
    return new LoginDTO(
        email: $this->validated('email'),
        password: $this->validated('password'),
    );
    }
}
