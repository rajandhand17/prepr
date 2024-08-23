<?php

namespace App\Http\Requests\Manage\MemberManagement;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DeleteMemberManagementRequest extends FormRequest
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
            'email'   => 'required|array',
            'email.*' => 'email|'.Rule::exists('member_management', 'email')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
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
        return[
            'email.required'    => __('responses.email_field_required'),
            'email.array'       => __('responses.member_manger_email'),
            'email.*.email'     => __('responses.valid_email_pattern'),
            'email.*.exists'    => __('responses.not_exists_email'),
        ];
    }
}
