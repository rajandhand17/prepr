<?php

namespace App\Http\Requests\Setting;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddAccountRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $base_rules = [
            'first_name'               => 'required|string',
            'last_name'                => 'required|string',
            'username'                 => 'required',
            'email'                    => 'required|email',
            'phone_number'             => 'required',
            'preferred_timezone'       => 'required',
            'preferred_language'       => 'required',
            'two_factor_verification'  => 'required|in:true,false',
        ];
        return $base_rules;
    }

    public function messages()
    {
        return [
            'first_name.required'           => __('responses.first_name_field_required'),
            'last_name.required'            => __('responses.last_name_field_required'),
            'purpose.numeric'               => __('responses.numeric_allowed_only'),
            'username.required'             => __('responses.required_field'),
            'email.required'                => __('responses.email_field_required'),
            'email.email'                   => __('responses.valid_email_pattern'),
            'phone_number.required'         => __('responses.required_field'),
            'preferred_timezone.required'   => __('responses.required_field'),
            'preferred_language.required'   => __('responses.required_field'),
            'two_factor_verification.required'=> __('responses.required_field'),
            'two_factor_verification.in'    => __('responses.true_or_false'),
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
}
