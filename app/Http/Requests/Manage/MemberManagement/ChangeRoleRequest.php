<?php

namespace App\Http\Requests\Manage\MemberManagement;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ChangeRoleRequest extends FormRequest
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
            'id'  => 'required|'.Rule::exists('member_management', 'uuid')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'role'=> 'required|'.Rule::exists('roles', 'display_name')->where(function ($query) {
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
            'id.required'       => __('responses.id_required'),
            'id.exists'         => __('responses.id_not_exists'),
            'role.required'     => __('responses.role_required'),
            'role.exists'       => __('responses.role_not_exists'),

        ];
    }
}
