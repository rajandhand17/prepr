<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterFormRequest extends FormRequest
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
            'first_name' => 'required|max:20|string',
            'last_name' => 'required|string|max:20',
            'username' => 'required|max:20|regex:/^[A-Za-z0-9_-]*$/|unique:users,username',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'email' => 'required|email|max:50|unique:users,email',
            'user_type' => 'required',
            'status' => 'required',
            'language_id'=>'required|numeric',
            'phone_number' => 'required|numeric|unique:users,phone_number',
            'country_code'=>'required|numeric'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }

    
    public function messages()
    {
        return [
            'email.required' => __('notification.notification_peeief'),
            'email.unique'=>__('responses.unique_email'), 
            'email.email'=>__('notification.notification_iea'),
            'email.max'=>__('responses.max_email'),
            'first_name.required'=>__("notification.notification_reg_fnr"),
            'first_name.max'=>__("notification.notification_reg_tfnmbg"),
            'first_name.string'=>__("responses.first_name_string"),
            'last_name.required'=>__("notification.notification_reg_lnr"),
            'last_name.max'=>__("notification.notification_reg_tlnmbg"),
            'last_name.string'=>__("responses.last_name_string"),
            'status.required'=>__("notification.notification_sfired"),
            'username.unique'=>__("responses.username_unique"),
            'username.max'=>__("notification.notification_reg_tunmbg"),
            'username.required'=>__("notification.notification_reg_unr"),
            'username.regex'=>__("notification.notification_reg_unc"),
            'user_type.required'=>__("notification.notification_reg_psut"),
            "password.required"=>__("notification.notification_reg_pass"),
            "password.min"=>__("responses.min_password"),
            "password_confirmation.required"=>__("notification.notification_reg_cpr"),
            "password_confirmation.same"=>__("responses.password_confirm_password"),
            "language_id.required"=>__("notification.notification_lfirs"),
            "language_id.numeric"=>__("responses.language_numeric"),
            'phone_number.required' =>__("notification.notification_ypnie"),
            'phone_number.numeric'=>__("responses.numeric"),
            'phone_number.unique'=>__("responses.already_number"),
            'country_code.required'=>__("responses.country_code_required"),
            'country_code.numeric'=>__("responses.country_code_numeric"),
        ];
    }
}
