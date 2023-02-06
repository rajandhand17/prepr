<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class submitResetPasswordFormRequest extends FormRequest
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
              'email' => 'required|email|exists:users,email',
              'password' => 'required|min:6',
              'password_confirmation' => 'required|same:password',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ],403));
    }

    public function messages()
    {
        return [
            'email.required' => __('notification.notification_peeief'),
            'email.exists'=>__('responses.not_exists_email'), 
            'email.email'=>__('notification.notification_iea'),
            "password.required"=>__('notification.notification_reg_pass'),
            "password.min"=>__('notification.min_password'),
            "password_confirmation.required"=>__('notification.notification_reg_cpr'),
            "password_confirmation.same"=>__('responses.password_confirm_password'),
        ];
    }
}
