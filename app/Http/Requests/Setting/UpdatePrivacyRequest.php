<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePrivacyRequest extends FormRequest
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
        $baseRules=[
            'profile_visibility'    =>'required|in:public,private,signed-in',
            'project_visibility'    =>'required|in:public,private',
            'friend_request'        =>'required|in:any-one,no-one',
        ];
        return $baseRules;
    }
    public function messages()
    {
        return [
            'profile_visibility.required'   => __('responses.required_fields'),
            'profile_visibility.in'         => __('responses.profile_privacy_in'),
            'project_visibility.required'   => __('responses.public_or_private'),
            'project_visibility.in'         => __('responses.public_or_private'),
            'friend_request.required'       => __('responses.required_fields'),
            'friend_request.in'             => __('responses.any_or_no_one'),
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
