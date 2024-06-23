<?php

namespace App\Http\Requests\Manage\Challenge;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateChallengeAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        $base_rules = [
            'assessment_type'       => 'in:open,closed,ai,none',
            'guidelines'            => 'required_if:assessment_type,open,closed,ai',
            'attachments'           => 'max:5120',
        ];

        if ($this->request->get('assessment_type') == 'closed') {
            $base_rules['visibility'] = 'in:users,hidden';
            $base_rules['members_email'] = 'array|required';
            $base_rules['members_email.*'] = 'email';
        }

        return $base_rules;
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
            'assessment_type.in'            => __('responses.choose_open_closed_ai'),
            'visibility.in'                 => __('responses.choose_users_hidden'),
            'visibility.required_if'        => __('responses.choose_users_hidden'),
            'guidelines.required_if'        => __('responses.guidelines_required'),
            'attachments.required_if'       => __('responses.mimes_image'),
            'members_email.required'        => __('responses.members_email_required'),
            'members_email.array'           => __('responses.members_email_array'),
            'members_email.email'           => __('responses.valid_email_pattern'),
        ];
    }
}
