<?php

namespace App\Http\Requests\Manage\Organization;

use App\Services\Manage\OrganizationService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use League\Container\Exception\NotFoundException;

class OnboardingOrganizationRequest extends FormRequest
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
        $base_rules = [];
        if (request()->route('slug')) {
            $organization = OrganizationService::getOrganizationBasedOnSlug(request()->route('slug'));
            if (!$organization) {
                throw new NotFoundException();
            }
            $base_rules = [
                'type'                          => 'required|array',
                'type.*'                        => 'in:assess,onboard,engage,grow',
                'business_challenge_tacklings'  => 'required|numeric|'.Rule::exists('business_challenge_tacklings', 'id')->where(function ($query) {
                        $query->whereNull('deleted_at');
                    }),
                'total_employees'               => 'required|integer',
                'website'                       => 'required|url',
                'category'                      => 'required|numeric|'.Rule::exists('categories', 'id')->where(function ($query) {
                        $query->whereNull('deleted_at');
                    }),
            ];
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
            'title.max'                                       => __('responses.title_content_255'),
            'title.unique'                                    => __('responses.organization_title_unique'),
            'category.required'                               => __('responses.category_required'),
            'category.numeric'                                => __('responses.category_numeric'),
            'category.exists'                                 => __('responses.category_not_exists'),
            'website.required'                                => __('responses.website_required'),
            'website.url'                                     => __('responses.website_valid_url_pattern'),
            'total_employees.integer'                         => __('responses.total_employees_integer'),
        ];
    }
}
