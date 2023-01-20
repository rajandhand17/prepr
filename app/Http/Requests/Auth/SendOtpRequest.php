<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
            "country_code"=>"required|numeric|",
            "phone_number"=>"required|numeric|exists:users",
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
            'country_code.required'=>"Please enter country code!",
            'country_code.numeric'=>"Please enter country code!",
            'phone_number.required'=>"Please enter phone number!",
            'phone_number.numeric'=>"Please enter phone number!",
            'phone_number.numeric'=>"Please enter phone number!",
            'phone_number.exists'=>"Please enter account related phone number!",

        ];
    }

}
