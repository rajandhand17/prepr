<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginFormRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    // Check if the value is a valid email
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        // If it's an email, check if it exists in the `email` field
                        if (!User::where('email', $value)->whereNull('deleted_at')->exists()) {
                            $fail(__('responses.not_exists_email'));
                        }
                    } else {
                        // If it's not an email, check if it exists as a username
                        if (!User::where('username', $value)->whereNull('deleted_at')->exists()) {
                            $fail(__('responses.not_exists_username'));
                        }
                    }
                },
            ],
            'password' => 'required|min:6',
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
            'email.required'   => __('responses.email_field_required'),
            'email.email'      => __('responses.valid_email_pattern'),
            'email.max'        => __('responses.max_content_50'),
            'email.exists'     => __('responses.not_exists_email'),
            'password.required'=> __('responses.password_required_field'),
            'password.min'     => __('responses.min_content_6'),
        ];
    }
}
