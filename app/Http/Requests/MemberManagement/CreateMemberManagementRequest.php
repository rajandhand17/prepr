<?php

namespace App\Http\Requests\MemberManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
        return [
            "type"=> 'required',
            "invite_type"=> 'required',
            "module_id"=> 'required',
            "module_type"=> 'required',
            "inviter_id"=> 'required',
            "role"=> 'required',
            "invite_status"=> 'required',
            "email"=> 'required',
            "email_status"=> 'required',
            "subject_line"=> 'required',
            "email_body"=> 'required',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ],422));
    }
    
    public function messages()
    {
        return[
            'type.required' => __('responses.type_required'),
            'invite_type.required' => __('responses.invite_type_required'),
            'module_id.required' => __('responses.module_id_required'),
            "module_type.required"=> __('responses.module_type_required'),
            "inviter_id.required"=> __('responses.inviter_id_required'),
            "role.required"=> __('responses.role_required'),
            "invite_status.required"=> __('responses.invite_status'),
            "email.required"=> __('responses.email'),
            "email_status.required"=> __('responses.email_status'),
            "subject_line.required"=> __('responses.subject_line'),
            "email_body.required"=> __('responses.email_body'),
        ];
    }
}
