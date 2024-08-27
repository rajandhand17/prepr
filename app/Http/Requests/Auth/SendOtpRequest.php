<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SendOtpRequest extends FormRequest
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
            'email'  => 'required|email|max:50|'.Rule::exists('users', 'email')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'purpose'=> 'required|in:forget_password,verify_email,two_factor_verification',
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
            'email.required'          => __('responses.email_field_required'),
            'email.email'             => __('responses.valid_email_pattern'),
            'email.max'               => __('responses.max_content_50'),
            'email.exists'            => __('responses.not_exists_email'),
            'purpose.required'        => __('responses.purpose_required'),

        ];
    }
}
