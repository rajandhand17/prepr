<?php

namespace App\Http\Requests\Manage\MemberManagement;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateMemberManagementRequest extends FormRequest
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
        $check_invite_type = $this->request->get('invite_type');

        $rules = [
            'type'         => 'required|in:invite,join_request,auto_created',
            'invite_type'  => 'required|in:email,csv',
            'role'         => 'required|exists:roles,display_name',
            'subject_line' => 'max:250',
            'email_body'   => 'max:2000',
            'auto_invite'  => 'required|in:yes,no,na',
        ];
        if ($check_invite_type == 'csv') {
            $rules['invite_email'] = 'required|mimes:csv,txt';
        }
        if ($check_invite_type == 'email') {
            $rules['invite_email'] = 'required|array';
            $rules['invite_email.*'] = 'required|email';
        }

        return $rules;
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
            'invite_email.required'     => __('responses.required_field'),
            'invite_email.mimes'        => __('responses.choose_csv_file'),
            'invite_email.array'        => __('responses.array'),
            'invite_email.*.required'   => __('responses.required_field'),
            'invite_email.*.email'      => __('responses.valid_email_pattern'),
            'type.required'             => __('responses.required_field'),
            'invite_type.required'      => __('responses.required_field'),
            'module_id.required'        => __('responses.required_field'),
            'module_id.exists'          => __('responses.organization_not_found'),
            'inviter_id.exists'         => __('responses.not_exists'),
            'inviter_id.required'       => __('responses.required_field'),
            'role.required'             => __('responses.required_field'),
            'role.exists'               => __('responses.not_exists'),
            'user_invite_email.required'=> __('responses.required_field'),
            'auto_invite.required'      => __('responses.required_field'),
            'auto_invite.in'            => __('responses.choose_yes_no'),
        ];
    }
}
