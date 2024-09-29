<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
        if ($this->request->has('links')) {
            $base_rules['links'] = 'array';
            $base_rules['links.*.title'] = 'max:255|required|string';
            $base_rules['links.*.social_link_id'] = 'required|'.Rule::exists('social_links', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            });
            $base_rules['links.*.path'] = 'required|max:1600';
        }
        if ($this->request->has('embed_media')) {
            $base_rules['embed_media'] = 'array';
            $base_rules['embed_media.*.id'] = 'nullable';
            $base_rules['embed_media.*.type'] = 'required|in:embedded_audio,embedded_video';
            $base_rules['embed_media.*.path'] = 'required|max:16000';
        }

        return $base_rules;
    }

    public function messages()
    {
        return [
            'links.array'                       => __('responses.add_links_array'),
            'links.*.title.required'            => __('responses.title_required'),
            'links.*.title.max'                 => __('responses.title_content_255'),
            'links.*.title.string'              => __('responses.string_data_allowed'),
            'links.*.social_link_id.required'   => __('responses.social_links_required'),
            'links.*.social_link_id.exists'     => __('responses.social_id_not_exists'),
            'links.*.path.required'             => __('responses.path_required'),
            'links.*.path.max'                  => __('responses.path_content_255'),
            'embed_media'                       => __('responses.add_embedded_media_array'),
            'embed_media.*.type.required'       => __('responses.type_required'),
            'embed_media.*.type.in'             => __('responses.embedded_type_in'),
            'embed_media.*.path.required'       => __('responses.path_required'),
            'embed_media.*.path.max'            => __('responses.max_content_255'),
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
