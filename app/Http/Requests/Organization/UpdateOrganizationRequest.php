<?php

namespace App\Http\Requests\Organization;

use App\Services\OrganizationService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrganizationRequest extends FormRequest
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
        $organization = OrganizationService::getOrganizationExistBasedOnSlug(request()->route('slug'));

        if ($organization) {
            $base_rules = [
                'title'         => 'required|max:255|unique:organizations,title,'.$organization->id,
                'description'   => 'required',
                'profile_image' => 'image|mimes:jpeg,jpg,png,webp|max:1024', //|dimensions:width=500,height=500
                'cover_image'   => 'image|mimes:jpeg,jpg,png,webp|max:1024',
                'category'      => 'required|exists:categories,id',
                'website'       => 'required|url',
            ];
        } else {
            $base_rules = [
                'title'         => 'required|max:255|unique:organizations,title',
                'description'   => 'required',
                'profile_image' => 'image|mimes:jpeg,jpg,png,webp|max:1024|dimensions:width=500,height=500',
                'cover_image'   => 'image|mimes:jpeg,jpg,png,webp|max:1024',
                'category'      => 'required|exists:categories,id',
                'website'       => 'required|url',
                'slug'          => 'required|max:255|unique:organizations,slug',
            ];
        }
        if ($this->request->has('organization_address')) {
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

        if ($this->request->has('organization_members')) {
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
            'title.unique'        => __('responses.organization_name_unique'),
            'title.max'           => __('responses.organization_name_max'),
            'profile_image.image' => __('responses.cover_image'),
            'cover_image.image'   => __('responses.profile_image'),
            'category.exists'     => __('responses.organization_category_exists'),
        ];
    }
}
