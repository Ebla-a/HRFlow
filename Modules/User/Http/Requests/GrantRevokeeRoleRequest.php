<?php

namespace Modules\User\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GrantRevokeeRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id'   => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id.required'   => 'User ID is required.',
            'id.integer'    => 'User ID must be a valid integer.',
            'id.exists'     => 'Selected user does not exist.',
            'role.required' => 'Role name is required.',
            'role.string'   => 'Role name must be a valid text string.',
            'role.exists'   => 'The specified role does not exist.',
        ];
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
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