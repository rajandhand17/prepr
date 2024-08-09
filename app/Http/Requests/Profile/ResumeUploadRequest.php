<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResumeUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $base_rules = [
                'resume' => 'required|file|max:1024|mimes:pdf,doc,docx,txt',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'file.required'                 => __('responses.required_field'),
            'file.max'                      => __('responses.mimes_file_max'),
            'file.mimeTypes'                => __('responses.files_mimes_image'),
            'file.mime_types'               => __('responses.files_mimes_image'),

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }
}
