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

        if ($this->has('assessment_type') && $this->input('assessment_type') != 'none') {
            $base_rules['assessment_title'] = 'required|array';
            $base_rules['assessment_title.*'] = 'required_if:assessment_type,open,closed,ai';
            $base_rules['assessment_description'] = 'required|array';
            $base_rules['assessment_description.*'] = 'required_if:assessment_type,open,closed,ai|string';
            $base_rules['assessment_score'] = 'required|array';
            $base_rules['assessment_score.*'] = 'required_if:assessment_type,open,closed,ai|numeric';
            $base_rules['assessment_weight'] = [
                'required',
                'array',
                'required_if:assessment_type,open,closed,ai',
                function ($attribute, $value, $fail) {
                    if (array_sum($value) != 100) {
                        $fail(__('responses.challenge_weight_should_be_100'));
                    }
                },
            ];
            $base_rules['assessment_weight.*'] = 'required_if:assessment_type,open,closed,ai|numeric';
        }

        return $base_rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $assessment_title = $this->input('assessment_title', []);
            $assessment_score = $this->input('assessment_score', []);

            $count_title = count($assessment_title);
            $count_score = count($assessment_score);

            if (($count_title > 0 || $count_score > 0) &&
                ($count_title !== $count_score)
            ) {
                $validator->errors()->add('assessment_data', __('responses.title_score_should_match_count'));
            }
        });
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
