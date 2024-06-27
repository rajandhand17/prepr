<?php

namespace App\Http\Requests\Manage\Organization;

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
            'title'                                 => 'required|max:255|unique:organizations,title',
            'description'                           => 'required',
            'profile_image'                         => 'image|mimes:jpeg,jpg,png,webp|max:1024|nullable',
            'cover_image'                           => 'image|mimes:jpeg,jpg,png,webp|max:1024|nullable',
            'category'                              => 'required|numeric|exists:categories,id',
            'website'                               => 'required|url',
            'slug'                                  => 'required|max:255|unique:organizations,slug',
            'status'                                => 'required|in:draft,publish,archive',
            'total_employees'                       => 'integer',
            'external_links'                        => 'array',
            'external_link_ids'                     => 'array|exists:social_links,id',
            'external_links.*'                      => 'url',
            'external_link_ids.*'                   => 'numeric',
        ];
        if ($this->request->has('organization_address')) {
            $base_rules['organization_address'] = 'array';
            $base_rules['organization_address.*.address_1'] = 'required|string';
            $base_rules['organization_address.*.address_2'] = 'string';
            $base_rules['organization_address.*.city'] = 'required|string';
            $base_rules['organization_address.*.state'] = 'required|string';
            $base_rules['organization_address.*.country'] = 'required|string';
            $base_rules['organization_address.*.zip_code'] = 'required|max:7';
            $base_rules['organization_address.*.latitude'] = 'required|string';
            $base_rules['organization_address.*.longitude'] = 'required|string';
        }
        if ($this->request->has('organization_members')) {
            $base_rules['organization_members'] = 'array';
            $base_rules['organization_members.*.name'] = 'required|string';
            $base_rules['organization_members.*.position'] = 'required|string';
            $base_rules['organization_members.*.image'] = 'image|mimes:jpeg,jpg,png,webp|max:1024';
        }

        if ($this->request->has('enable_custom_login_and_registration') && $this->input('enable_custom_login_and_registration') == 'yes') {
            $base_rules['use_main_org_logo'] = 'required_if:enable_custom_login_and_registration,yes|in:yes,no';
            $base_rules['custom_login_url'] = 'required_if:enable_custom_login_and_registration,yes';
            $base_rules['custom_logo_image'] = 'image|mimes:jpeg,jpg,png,webp|max:1024|';
            $base_rules['custom_hero_image'] = 'image|mimes:jpeg,jpg,png,webp|max:1024|';
            $base_rules['custom_background_color'] = ['regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'];
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
            'title.required'                                    => __('responses.title_required'),
            'title.max'                                         => __('responses.title_content_255'),
            'title.unique'                                      => __('responses.organization_title_unique'),
            'description.required'                              => __('responses.description_required'),
            'profile_image.image'                               => __('response.type_image'),
            'profile_image.mimes'                               => __('responses.mimes_image'),
            'profile_image.max'                                 => __('responses.mimes_image_max'),
            'profile_image.nullable'                            => __('responses.mimes_image_max'),
            'cover_image.image'                                 => __('response.type_image'),
            'cover_image.mimes'                                 => __('responses.mimes_image'),
            'cover_image.max'                                   => __('responses.mimes_image_max'),
            'cover_image.nullable'                              => __('responses.mimes_image_max'),
            'category.required'                                 => __('responses.category_required'),
            'category.numeric'                                  => __('responses.category_numeric'),
            'category.exists'                                   => __('responses.category_not_exists'),
            'website.required'                                  => __('responses.website_required'),
            'website.url'                                       => __('responses.website_valid_url_pattern'),
            'slug.required'                                     => __('responses.slug_required'),
            'slug.max'                                          => __('responses.slug_max'),
            'slug.unique'                                       => __('responses.unique_slug_data'),
            'status.required'                                   => __('responses.status_required'),
            'status.numeric'                                    => __('responses.status_numeric'),
            'status.in'                                         => __('responses.status_in'),
            'organization_address.array'                        => __('responses.status_array'),
            'organization_address.*.address_1.required'         => __('responses.organization_address_required'),
            'organization_address.*.address_1.string'           => __('responses.organization_address_string'),
            'organization_address.*.address_2.string'           => __('responses.organization_address_string'),
            'organization_address.*.city.required'              => __('responses.city_required'),
            'organization_address.*.city.string'                => __('responses.city_string'),
            'organization_address.*.state.required'             => __('responses.state_required'),
            'organization_address.*.state.string'               => __('responses.state_string'),
            'organization_address.*.country.required'           => __('responses.country_required'),
            'organization_address.*.country.string'             => __('responses.country_string'),
            'organization_address.*.zip_code.required'          => __('responses.zip_code_required'),
            'organization_address.*.zip_code.max'               => __('responses.zip_code_max'),
            'organization_address.*.latitude.required'          => __('responses.latitude_required'),
            'organization_address.*.latitude.string'            => __('responses.latitude_string'),
            'organization_address.*.longitude.required'         => __('responses.longitude_required'),
            'organization_address.*.longitude.string'           => __('responses.longitude_string'),
            'organization_members.array'                        => __('responses.organization_members_array'),
            'organization_members.*.name.required'              => __('responses.organization_members_name_required'),
            'organization_members.*.name.string'                => __('responses.organization_members_name_string'),
            'organization_members.*.position.required'          => __('responses.organization_members_position_required'),
            'organization_members.*.position.string'            => __('responses.organization_members_position_string'),
            'organization_members.*.image.mimes'                => __('responses.mimes_image'),
            'organization_members.*.image.image'                => __('response.type_image'),
            'organization_members.*.image.max'                  => __('responses.mimes_image_max'),
            'organization_members.*.image.dimensions'           => __('responses.dimensions'),
            'external_links.array'                              => __('responses.external_links_array'),
            'external_links.url'                                => __('responses.external_links_valid_url_pattern'),
            'external_link_ids.exists'                          => __('responses.external_link_ids_not_exists'),
            'external_link_ids.array'                           => __('responses.external_link_ids_array'),
            'external_link_ids.numeric'                         => __('responses.external_link_ids_numeric'),
            'enable_custom_login_and_registration.required'     => __('responses.enable_custom_login_and_registration_required'),
            'enable_custom_login_and_registration.in'           => __('responses.enable_custom_login_and_registration_in'),
        ];
    }
}
