<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CheckUserRequest extends FormRequest
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
            'username' => 'required|max:20|regex:/^[A-Za-z0-9_-]*$/||unique:users,username',
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
        return [
            'username.max'=>__("responses.username_max"),
            'username.required'=>__("responses.username_required"),
            'username.unique'=>__("responses.username_unique"),
            'username.regex'=>__("notification.notification_reg_unc"),
        ];
    }
}
