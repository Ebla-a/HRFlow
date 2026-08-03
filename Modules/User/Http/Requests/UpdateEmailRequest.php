<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'email' => [

                'required',

                'email',

                'max:255',

                Rule::unique('users', 'email')
                    ->ignore($this->route('user')->id),

            ],

        ];
    }
}