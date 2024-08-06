<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileUploadRequest extends FormRequest
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
            'file_upload' => 'array',
            'file_upload.*' => 'mimes:jpg,jpeg,webp,png,pdf,mp3,doc,docx,xlsx,xls,pptx,pptm,odp,ppt,mp4,mov,wmv,avi,webm,mkv,mpeg-2|max:153600',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'file_upload.required'             => __('responses.required_field'),
            'file_upload.max'                  => __('responses.mimes_file_max'),
            'file_upload.mimes'                => __('responses.files_mimes_image'),
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
