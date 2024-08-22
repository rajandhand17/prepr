<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DeleteMediaResourceModuleRequest extends FormRequest
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
        return [
            'media_id' => 'required|'.Rule::exists('resource_module_details', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'type'     => 'required|in:document,video,audio,embedded_video,embedded_audio,url,image,embedded_cover_video',
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

    public function messages()
    {
        return [
            'media_id.required'                 => __('responses.media_id_required'),
            'media_id.exists'                   => __('responses.media_id_not_exists'),
        ];
    }
}
