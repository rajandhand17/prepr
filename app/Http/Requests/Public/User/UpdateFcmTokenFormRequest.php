<?php

namespace App\Http\Requests\Public\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFcmTokenFormRequest extends FormRequest
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
        return [
            'fcm_token' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'fcm_token.required'      => __('responses.fcm_token_required'),
            'fcm_token.string'        => __('responses.fcm_token_string'),
        ];
    }
}
