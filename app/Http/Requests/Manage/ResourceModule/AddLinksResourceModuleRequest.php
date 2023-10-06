<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddLinksResourceModuleRequest extends FormRequest
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
            'title'                  => 'required|array',
            'title.*'                => 'max:255',
            'social_link_id'         => 'required|array',
            'social_link_id.*'       => 'exists:social_links,id',
            'type'                   => 'required|array',
            'type.*'                 => 'max:255|in:embedded_audio,audio,embedded_video,video,url',
            'path'                   => 'required|array',

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
            'title.required'                  => __('responses.title_required'),
            'title.unique'                    => __('responses.lab_program_title_unique'),
            'title.*.max'                     => __('responses.mimes_image_max'),
            'path.unique'                     => __('responses.lab_program_title_unique'),
            'social_link_id.unique'           => __('responses.lab_program_title_unique'),
            'social_link_id.exists'           => __('responses.social_id_not_exists'),
            'request_type.in'                 => __('responses.request_type_in'),
            'request_type.required'           => __('responses.request_type_required'),
            'type.required'                   => __('responses.type_required'),
            'type.array'                      => __('responses.type_array'),
            'type.*.max'                      => __('responses.type_content_255'),
            'type.*.in'                       => __('responses.embedded_type_in'),
            'path.required'                   => __('responses.path_required'),
            'path.array'                      => __('responses.path_array'),
        ];
    }
}
