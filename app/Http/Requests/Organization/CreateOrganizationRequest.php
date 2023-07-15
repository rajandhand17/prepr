<?php

namespace App\Http\Requests\Organization;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $base_rules = [
            'title'           => 'required|max:255|unique:organizations,title',
            'description'     => 'required',
            'profile_image'   => 'image|mimes:jpeg,jpg,png,webp|max:1024|nullable',
            'cover_image'     => 'image|mimes:jpeg,jpg,png,webp|max:1024|nullable',
            'category'        => 'required|numeric|exists:categories,id',
            'website'         => 'required|url',
            'slug'            => 'required|max:255|unique:organizations,slug',
            'status'          => 'required|in:draft,publish,archive',
        ];
        if($this->request->has('organization_address')) {
            $base_rules['organization_address'] = 'array';
            $base_rules['organization_address.*.address_1'] = 'required|string';
            $base_rules['organization_address.*.address_2'] = 'required|string';
            $base_rules['organization_address.*.city'] = 'required|string';
            $base_rules['organization_address.*.state'] = 'required|string';
            $base_rules['organization_address.*.country'] = 'required|string';
            $base_rules['organization_address.*.zip_code'] = 'required|string';
            $base_rules['organization_address.*.latitude'] = 'required|string';
            $base_rules['organization_address.*.longitude'] = 'required|string';
        }
        if($this->request->has('organization_members')) {
            $base_rules['organization_members'] = 'array';
            $base_rules['organization_members.*.name'] = 'required|string';
            $base_rules['organization_members.*.position'] = 'required|string';
            $base_rules['organization_members.*.image'] = 'image|mimes:jpeg,jpg,png,webp|max:1024|dimensions:width=500,height=500';
        }

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
        return[
            'title.required'       => __('responses.required_field'),
            'title.max'            => __('responses.max_content_255'),
            'title.unique'         => __('responses.already_exists'),
            'description.required' => __('responses.required_field'),
            'profile_image.image'  => __('response.type_image'),
            'profile_image.mimes'  => __('responses.mimes_image'),
            'profile_image.max'  => __('responses.mimes_image_max'),
            'profile_image.nullable'  => __('responses.mimes_image_max'),
            'cover_image.image'  => __('response.type_image'),
            'cover_image.mimes'  => __('responses.mimes_image'),
            'cover_image.max'  => __('responses.mimes_image_max'),
            'cover_image.nullable'  => __('responses.mimes_image_max'),
            'category.required'    => __('responses.required_field'),
            'category.numeric'    => __('responses.numeric_data_allowed'),
            'category.exists'      => __('responses.not_exists'),
            'website.required'      => __('responses.required_field'),
            'website.url'      => __('responses.valid_url_pattern'),
            'slug.required'      => __('responses.required_field'),
            'slug.max'      => __('responses.required_field'),
            'slug.unique'      => __('responses.required_field'),
            'status.required'      => __('responses.required_field'),
            'status.numeric'      => __('responses.numeric_data_allowed'),
            'status.in'      => __('responses.choose_draft_publish_archive'),
            'organization_address.array'      => __('responses.array'),
            'organization_address.*.address_1.required'      => __('responses.array'),
            'organization_address.*.address_1.string'      => __('responses.string_data_allowed'),
            'organization_address.*.address_2.required'      => __('responses.required_field'),
            'organization_address.*.address_2.string'      => __('responses.string_data_allowed'),
            'organization_address.*.city.required'      => __('responses.required_field'),
            'organization_address.*.city.string'      => __('responses.string_data_allowed'),
            'organization_address.*.state.required'      => __('responses.required_field'),
            'organization_address.*.state.string'      => __('responses.string_data_allowed'),
            'organization_address.*.country.required'      => __('responses.required_field'),
            'organization_address.*.country.string'      => __('responses.string_data_allowed'),
            'organization_address.*.zip_code.required'      => __('responses.required_field'),
            'organization_address.*.zip_code.string'      => __('responses.string_data_allowed'),
            'organization_address.*.latitude.required'      => __('responses.required_field'),
            'organization_address.*.latitude.string'      => __('responses.string_data_allowed'),
            'organization_address.*.longitude.required'      => __('responses.required_field'),
            'organization_address.*.longitude.string'      => __('responses.string_data_allowed'),
            'organization_members.array'=>__('responses.array'),
            'organization_members.*.name.required'=>__('responses.required_field'),
            'organization_members.*.name.string'=>__('responses.string_data_allowed'),
            'organization_members.*.position.required'=>__('responses.required_field'),
            'organization_members.*.position.string'=>__('responses.string_data_allowed'),
            'organization_members.*.image.mimes'  => __('responses.mimes_image'),
            'organization_members.*.image.image'  => __('response.type_image'),
            'organization_members.*.image.max'  => __('responses.mimes_image_max'),
            'organization_members.*.image.dimensions'  => __('responses.mimes_image_max'),
            
        ];
    }
}
