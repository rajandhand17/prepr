<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ResetPasswordFormRequest extends FormRequest
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
            'email'                 => 'required|email|'.Rule::exists('users', 'email')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'otp'                   => 'required',
        ];
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
        return [
            'email.required'                 => __('responses.email_field_required'),
            'email.email'                    => __('responses.valid_email_pattern'),
            'email.exists'                   => __('responses.not_exists_email'),
            'password.required'              => __('responses.password_required_field'),
            'password.min'                   => __('responses.min_content_6'),
            'password_confirmation.required' => __('responses.password_confirmation_required_field'),
            'password_confirmation.same'     => __('responses.match_confirmed_password'),
            'otp.required'                   => __('responses.required_otp'),
        ];
    }
}
