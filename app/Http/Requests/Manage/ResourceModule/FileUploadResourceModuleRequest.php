<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileUploadResourceModuleRequest extends FormRequest
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
            'file_upload'            => 'required|array',
            'file_upload.*'          => 'mimes:jpg,jpeg,webp,png,pdf,mp3,doc,docx,xlsx,xls,pptx,pptm,odp,ppt,mp4,mov,wmv,avi,webm,mkv,mpeg-2|max:153600',
        ];

        return $base_rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }

    public function messages()
    {
        return [
            'file_upload.required'                => __('responses.file_upload_required'),
            'file_upload.array'                   => __('responses.file_upload_array'),
            'file_upload.*.max'                   => __('responses.file_upload_max'),
            'file_upload.*.mimes'                 => __('responses.resource_file_upload_mimes_image'),
        ];
    }
}
