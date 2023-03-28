<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateOrganizationRequest extends FormRequest
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
            'name' => 'max:255|unique:organizations,name',
            'description'=>'string',
            'profile_image'=>'image',
            'cover_image'=>'image',
            'category'=>'exists:categories,id'
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
        return[
            'name.unique'=>__('responses.organization_name_unique'), 
            'name.max'=>__('responses.organization_name_max'),
            'profile_image.image'=>__('responses.cover_image'),
            'cover_image.image'=>__('responses.profile_image'),
            'category.exists'=>__('responses.organization_category_exists'),
            
        ];
    }
}
