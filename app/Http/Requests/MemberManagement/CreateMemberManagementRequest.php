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
       $check_csv_request=$this->request->get('invite_type');
        $rules= [
            "type"=> 'required',
            "invite_type"=> 'required',
            "module_id"=> 'required|exists:organizations,id',
            "invite_email"=>'required',
            "role"=> 'required',
            "inviter_id"=> 'required|exists:users,id',
            "subject_line"=> 'required',
            "email_body"=> 'required',
            "invite_status"=> 'required',
       
        ];
        if($check_csv_request=="csv"){
            $rules["invite_email"]='required|mimes:csv';
        }
        
        return $rules;
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
            'invite_email.required'=>__('responses.invitee_id_required'),
            'invite_email.mimes'=>__('responses.invitee_id_required'),
            'type.required' => __('responses.type_required'),
            'invite_type.required' => __('responses.invite_type_required'),
            'module_id.required' => __('responses.module_id_required'),
            'module_id.exists'=>__('notification.notification_onf'),
            'inviter_id.exists' => __('responses.user_id_not_exists'),
            "inviter_id.required"=> __('responses.inviter_id_required'),
            "role.required"=> __('responses.role_required'),
            "user_invite_email.required"=> __('responses.email_required'),
            "subject_line.required"=> __('responses.subject_line'),
            "email_body.required"=> __('responses.email_body'),
            "invite_status"=>__('responses.auto_invite_status'),
        ];
    }
}
