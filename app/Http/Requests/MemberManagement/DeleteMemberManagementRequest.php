<?php

namespace App\Http\Requests\MemberManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\MemberManagement as MemberManagements;
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
            'id' =>'required|array|exists:member_management,id'
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
            'id.required'=>__('responses.member_manager_id_required'),
            'id.array'=>__('responses.member_manager_id_required'),
            'id.exists'=>__('responses.member_manager_not_exists'),
        ];
    }
}
