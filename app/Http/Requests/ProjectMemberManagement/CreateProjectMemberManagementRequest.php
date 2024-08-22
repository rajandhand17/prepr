<?php

namespace App\Http\Requests\ProjectMemberManagement;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateProjectMemberManagementRequest extends FormRequest
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
        $check_invite_type = $this->request->get('invite_type');

        $rules = [
            'invite_type'       => 'required|in:email,csv',
            'subject_line'      => 'max:250',
            'email_body'        => 'max:2000',
            'skills'            => 'array',
            'skills.*'          => 'numeric|'.Rule::exists('skills', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'recruiting_status' => 'in:yes,no',
        ];
        if ($check_invite_type == 'csv') {
            $rules['invite_email'] = 'required|mimes:csv,txt';
        }
        if ($check_invite_type == 'email') {
            $rules['invite_email'] = 'required|array';
            $rules['invite_email.*'] = 'required|email';
        }

        return $rules;
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
            'invite_email.required'     => __('responses.invite_email_required'),
            'invite_email.mimes'        => __('responses.choose_csv_file'),
            'invite_email.array'        => __('responses.invite_email_array'),
            'invite_email.*.required'   => __('responses.invite_email_required'),
            'invite_email.*.email'      => __('responses.valid_email_pattern'),
            'invite_type.required'      => __('responses.invite_type_required'),
        ];
    }
}
