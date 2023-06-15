<?php

namespace App\Http\Requests\Lab;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LabStoreRequest extends FormRequest
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
            "organisation"=>"required",
            "title"=>"required",
            "description"=>"required",
            "category"=>"required",
            "privacy"=>"required",
            "member_type"=>"required",
            "member"=>"required",
            "country"=>"required",
            "latitute"=>"required",
            "longitude"=>"required",
            "address"=>"required",
            "city"=>"required",
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
        
      ];   
    }
}
