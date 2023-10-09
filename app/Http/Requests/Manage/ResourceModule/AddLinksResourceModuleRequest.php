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
        if ($this->request->has('add_links')) {
            $base_rules['add_links'] = 'array';
            $base_rules['add_links.*.title'] = 'max:255|required|string';
            $base_rules['add_links.*.social_link_id'] = 'required|exists:social_links,id';
            $base_rules['add_links.*.path'] = 'required|max:255';
        }
        if ($this->request->has('add_embedded_media')) {
            $base_rules['add_embedded_media'] = 'array';
            $base_rules['add_embedded_media.*.type'] = 'required|in:embedded_audio,embedded_video,url';
            $base_rules['add_embedded_media.*.path'] = 'required|max:255';
        }

        return $base_rules;
    }

    public function messages()
    {
        return [
            'add_links.array'                       => __('responses.add_links_array'),
            'add_links.*.title.required'            => __('responses.title_required'),
            'add_links.*.title.max'                 => __('responses.title_content_255'),
            'add_links.*.title.string'              => __('responses.string_data_allowed'),
            'add_links.*.social_link_id.required'   => __('responses.social_links_required'),
            'add_links.*.social_link_id.exists'     => __('responses.social_id_not_exists'),
            'add_links.*.path.required'             => __('responses.path_required'),
            'add_links.*.path.max'                  => __('responses.path_content_255'),
            'add_embedded_media'                    => __('responses.add_embedded_media_array'),
            'add_embedded_media.*.type.required'    => __('responses.type_required'),
            'add_embedded_media.*.type.in'          => __('responses.embedded_type_in'),
            'add_embedded_media.*.path.required'    => __('responses.path_required'),
            'add_embedded_media.*.path.max'         => __('responses.max_content_255'),
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
