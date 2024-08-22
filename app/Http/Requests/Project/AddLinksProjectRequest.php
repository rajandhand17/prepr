<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddLinksProjectRequest extends FormRequest
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
            'external_links'         => 'array|required',
            'external_link_ids'      => 'array|required|'.Rule::exists('social_links', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'external_links.*'       => 'url',
            'external_link_ids.*'    => 'numeric',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'external_links.array'           => __('responses.external_links_array'),
            'external_links.url'             => __('responses.external_links_valid_url_pattern'),
            'external_link_ids.exists'       => __('responses.external_link_ids_not_exists'),
            'external_link_ids.array'        => __('responses.external_link_ids_array'),
            'external_link_ids.numeric'      => __('responses.external_link_ids_numeric'),
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
