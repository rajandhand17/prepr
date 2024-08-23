<?php

namespace App\Http\Requests\Manage\Unified;

use App\Helpers\CryptHelper;
use App\Http\Requests\BaseRequest;
use App\Rules\UnifiedStateRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UnifiedUserInviteRequest extends BaseRequest
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
        $rules = [
            'state'           => ['required', new UnifiedStateRule()],
            'members'         => ['required', 'array'],
            'members.*.name'  => ['required', 'string'],
            'members.*.email' => ['required', 'email'],
            'subject_line'    => 'max:250',
            'email_body'      => 'max:2000',
        ];
        $roleRule = $this->getRoleRule();
        if ($roleRule) {
            $rules['members.*.role'] = $roleRule;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'state.required'        => __('responses.unified_state_required'),
            'members.required'      => __('responses.unified_members_field_required'),
            'members.array'         => __('responses.unified_members_field_must_be_array'),
            'members.*.email.email' => __('responses.unified_valid_email_pattern'),
        ];
    }

    public function getRoleRule(): false|string
    {
        $state = CryptHelper::decrypt($this->get('state'));
        if ($state) {
            if (data_get($state, 'usage_type') === 'organization_member_invite') {
                return 'required|'.Rule::exists('roles', 'display_name')->where(function ($query) {
                    $query->whereNull('deleted_at');
                });
            }
        }

        return false;
    }

    public function formatted(): array
    {
        return [
            'state'        => CryptHelper::decrypt($this->get('state')),
            'members'      => $this->get('members'),
            'subject_line' => $this->get('subject_line'),
            'email_body'   => $this->get('email_body'),
        ];
    }
}
