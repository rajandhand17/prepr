<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileUploadRequest extends FormRequest
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

            'profile_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'profile_image.required'             => __('responses.required_field'),
            'profile_image.max'                  => __('responses.mimes_image_max'),
            'profile_image.mimes'                => __('responses.mimes_image'),
            'profile_image.image'                => __('responses.image'),
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
