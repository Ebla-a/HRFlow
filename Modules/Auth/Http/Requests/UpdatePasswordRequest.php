<?php

namespace Modules\Auth\Http\Requests;

use Modules\Auth\App\DTOs\ChangePasswordDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;



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

    public function messages(): array
    {
    return [
        'current_password.required'         => __('Current password is required'),
        'current_password.string'           => __('Current password must be a valid string'),
        'current_password.current_password' => __('The provided password does not match your current password'),

        'password.required'                 => __('New password is required'),
        'password.string'                   => __('New password must be a valid string'),
        'password.confirmed'                => __('Password confirmation does not match'),
        'password.different'                => __('New password must be different from current password'),
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
    

    public function toDTO(): ChangePasswordDTO
    { 
    return new ChangePasswordDTO(
        currentPassword: $this->validated('current_password'),
        password: $this->validated('password'),
    );
    }
}
 