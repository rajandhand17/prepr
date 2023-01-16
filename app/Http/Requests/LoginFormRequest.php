<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
            'username' => 'required|string',
            'password' => 'required|min:6',
            ];
      }

      public function failedValidation(Validator $validator)
      {
          throw new HttpResponseException(response()->json([
              'success'   => true,
              'message'   => 'Validation errors',
              'data'      => $validator->errors()
          ]));
      }

      public function messages()
      {
          return [
              'username.required'=>"Please enter username!",
              "password.required"=>"Please enter password!",
              "password.min"=>"Please enter password minimum 6 characters!",
              ];
      }


   }
