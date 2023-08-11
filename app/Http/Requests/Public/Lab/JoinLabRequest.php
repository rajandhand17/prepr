<?php

namespace App\Http\Requests\Public\Lab;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class JoinLabRequest extends FormRequest
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
            'type'         => 'required|in:join_request',
            'invite_type'  => 'required|in:join_request',
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

    public function messages()
    {
        return [
            'type.required'             => __('responses.type_required'),
            'invite_type.required'      => __('responses.invite_type_required'),
            'type.in'                   => __('responses.join_type_in'),
            'invite_type.in'            => __('responses.join_invite_type_in'),
        ];
    }
}
