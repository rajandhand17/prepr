<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEmbedMediaResourceModuleRequest extends FormRequest
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
            'type'                  => 'required|array',
            'type.*'                => 'max:255|in:embedded_audio,audio,embedded_video,video',
            'path'                  => 'required|array',

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
            'type.required'                     => __('responses.type_required'),
            'type.array'                        => __('responses.type_array'),
            'type.*.max'                        => __('responses.type_content_255'),
            'type.*.in'                         => __('responses.embedded_type_in'),
            'path.required'                     => __('responses.path_required'),
            'path.array'                        => __('responses.path_array'),
        ];
    }
}
