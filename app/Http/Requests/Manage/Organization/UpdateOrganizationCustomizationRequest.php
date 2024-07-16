<?php

namespace App\Http\Requests\Manage\Organization;

use App\Services\Manage\OrganizationService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use League\Container\Exception\NotFoundException;

class UpdateOrganizationCustomizationRequest extends FormRequest
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
        $organization = OrganizationService::getOrganizationBasedOnSlug(request()->route('slug'));
        if (!$organization) {
            throw new NotFoundException();
        }
        $base_rules = [
            'enable_custom_login_and_registration'      => 'nullable|in:yes,no,none',
            'use_main_org_logo'                         => 'required_if:enable_custom_login_and_registration,yes|in:yes,no',
            'custom_logo_image'                         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:1024|',
            'custom_hero_image'                         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:1024|',
            'custom_background_color'                   => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
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
        return[
            'enable_custom_login_and_registration.in'           => __('responses.status_in'),
            'use_main_org_logo.required_if'                     => __('responses.use_main_org_logo_required'),
            'use_main_org_logo.in'                              => __('responses.use_main_org_logo_in'),
            'custom_logo_image.image'                           => __('responses.type_image'),
            'custom_logo_image.mimes'                           => __('responses.mimes_image'),
            'custom_logo_image.max'                             => __('responses.mimes_image_max'),
            'custom_hero_image.image'                           => __('responses.type_image'),
            'custom_hero_image.mimes'                           => __('responses.mimes_image'),
            'custom_hero_image.max'                             => __('responses.mimes_image_max'),
        ];
    }
}
