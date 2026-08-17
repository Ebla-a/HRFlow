<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;



class UploadEmployeeDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
    return [
        'title.required' => __('Title is required'),
        'title.string'   => __('Title must be a valid string'),
        'title.max'      => __('Title field may not be greater than 255 characters'),

        'type.required'  => __('Type is required'),
        'type.string'    => __('Type must be a valid string'),
        'type.max'       => __('Type field may not be greater than 50 characters'),

        'file.required'  => __('File is required'),
        'file.file'      => __('The uploaded item must be a valid file'),
        'file.mimes'     => __('File must be a file of type: pdf, jpg, jpeg, png'),
        'file.max'       => __('File size may not be greater than 10240 kilobytes'),
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
}