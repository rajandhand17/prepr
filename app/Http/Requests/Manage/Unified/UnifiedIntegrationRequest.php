<?php

namespace App\Http\Requests\Manage\Unified;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class UnifiedIntegrationRequest extends BaseRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $slugValidation = $this->getSlugValidationRules($this->get('usage_type'));

        return [
            'component_slug' => ['required', 'string', $slugValidation],
            'usage_type'     => ['required', 'string', Rule::in(collect(config('unified.usage_types'))->keys()->toArray())],
        ];
    }

    public function messages(): array
    {
        return [
            'component_slug.required' => __('responses.unified_slug_required'),
            'component_slug.exists'   => $this->getSlugValidationMessage($this->get('usage_type', '')),
            'usage_type.required'     => __('responses.unified_usage_type_required'),
            'usage_type.in'           => __('responses.unified_invalid_usage_type_value'),
        ];
    }

    /**
     * @param string $usage
     *
     * @return Exists|string
     */
    public function getSlugValidationRules($usage): Exists|string
    {
        return match ($usage) {
            'organization_member_invite' => Rule::exists('organizations', 'slug'),
            'lab_member_invite'          => Rule::exists('labs', 'slug'),
            'challenge_member_invite'    => Rule::exists('challenges', 'slug'),
            'lab_program_member_invite'  => Rule::exists('lab_programs', 'slug'),
            default                      => '',
        };
    }

    /**
     * @param string $usage
     *
     * @return Exists|string
     */
    public function getSlugValidationMessage(string $usage): Exists|string
    {
        return match ($usage) {
            'organization_member_invite' => __('responses.organization_not_found'),
            'lab_member_invite'          => __('responses.lab_slug_not_found'),
            'challenge_member_invite'    => __('responses.challenge_not_found'),
            'lab_program_member_invite'  => __('responses.lab_program_not_found'),
            default                      => '',
        };
    }
}
