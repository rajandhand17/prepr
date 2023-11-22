<?php

namespace App\Http\Requests\Manage\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddPersonalDetailRequest extends FormRequest
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
        $base_rules= [
            'user_id'       =>'required|exists:users,id',
            'age'           =>'required',
            'about'         =>'required',
            'purpose'       =>'required',
            'gender'        =>'required',
            'date_of_birth' =>'required',
        ];
        return $base_rules;
    }
    public function messages()
    {
        return [
            'user_id.required'=>__('responses'),
            'age.required'=>__('responses'),
            'about.required'=>__('responses'),
            'purpose.required'=>__('responses'),
            'gender.required'=>__('responses'),
            'date_of_birth.required'=>__('responses'),
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
