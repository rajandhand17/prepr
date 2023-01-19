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
            'username' => 'required|string|max:20|regex:/^[A-Za-z0-9_-]*$/|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'email' => 'required|string|email|max:50|unique:users',
            'user_type' => 'required',
            'status' => 'required|min:1',
            'language_id'=>'required|numeric|min:1',
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
            'email.required' => 'Please enter your email!',
            'email.unique'=>'This email is already registered!',
            'email.email'=>'Please enter only emails!',
            'first_name.required'=>'Please enter first name!',
            'first_name.max'=>'Please enter first name under 20 characters!',
            'first_name.string'=>'Please enter string in first name!',
            'last_name.required'=>'Please enter last name!',
            'last_name.max'=>'Please enter last name under 20 characters!',
            'last_name.string'=>'Please enter string in first name!',
            'status.required'=>"Please enter status!",
            'username.unique'=>"Username already taken,please choose another-one!",
            'username.max'=>"Please enter username under 20 characters!",
            'username.required'=>"Please enter username!",
            'user_type.required'=>"Please enter usertype!",
            "password.required"=>"Please enter password!",
            "password.min"=>"Please enter password minimum 6 characters!",
            "password_confirmation.required"=>"Please enter confirm password!",
            "password_confirmation.same"=>"Please enter same password in confirm password as password!",
            "language_id.required"=>"Please choose language!",
            "language_id.numeric"=>"Please enter correct language id!",
        ];
    }
}
