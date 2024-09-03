<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSettingRequest extends FormRequest
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
        $activity = $this->route('activity');
        $base_rules = [];
        if ($activity == 'account') {
            $base_rules = [
                'first_name'               => 'required|string',
                'last_name'                => 'required|string',
                'username'                 => 'required',
                'phone_number'             => 'regex:/^\+?(\d{1,3})?\s?\(?(\d{3})\)?[\s.-]?(\d{3})[\s.-]?(\d{4})$/',
                'preferred_timezone'       => 'required|in:EST,CST,MST,PST,AST,NST,IST',
                'preferred_language'       => 'required',
                'two_factor_verification'  => 'required|in:yes,no',

            ];
        } elseif ($activity == 'privacy') {
            $base_rules = [
                'profile_visibility'       => 'required|in:public,private,signed-in',
                'project_visibility'       => 'required|in:public,private',
                'friend_request_privacy'   => 'required|in:public,private',
            ];
        } elseif ($activity == 'password') {
            $base_rules = [
                'password'                 => 'required|min:8|max:14|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'password_confirmation'    => 'required|same:password',
            ];
        } elseif ($activity == 'notification') {
            $base_rules = [
                'communication'            => 'in:subscribe,unsubscribe',
                'network_summary'          => 'in:subscribe,unsubscribe',
                'lab_summary'              => 'in:unsubscribe,monthly,weekly',
                'challenge_summary'        => 'in:unsubscribe,monthly,weekly',
                'challenge_recommendation' => 'in:unsubscribe,monthly,weekly',
            ];
        }

        return $base_rules;
    }

    public function messages()
    {
        return [
            'profile_visibility.required'      => __('responses.required_fields'),
            'profile_visibility.in'            => __('responses.profile_privacy_in'),
            'project_visibility.required'      => __('responses.public_or_private'),
            'project_visibility.in'            => __('responses.public_or_private'),
            'friend_request.required'          => __('responses.required_fields'),
            'friend_request.in'                => __('responses.public_or_private'),
            'first_name.required'              => __('responses.first_name_field_required'),
            'last_name.required'               => __('responses.last_name_field_required'),
            'purpose.numeric'                  => __('responses.numeric_allowed_only'),
            'username.required'                => __('responses.required_field'),
            'email.required'                   => __('responses.email_field_required'),
            'email.email'                      => __('responses.valid_email_pattern'),
            'phone_number.regex'               => __('responses.regex_phone_number'),
            'preferred_timezone.required'      => __('responses.required_field'),
            'preferred_language.required'      => __('responses.required_field'),
            'two_factor_verification.required' => __('responses.required_field'),
            'two_factor_verification.in'       => __('responses.choose_yes_no'),
            'password.required'                => __('responses.password_required_field'),
            'password.min'                     => __('responses.min_content_6'),
            'password.max'                     => __('responses.max_content_14'),
            'password_confirmation.required'   => __('responses.password_confirmation_required_field'),
            'password_confirmation.same'       => __('responses.match_confirmed_password'),
            'communication.required'           => __('responses.required_fields'),
            'communication.in'                 => __('responses.subscribed_or_unsubscribed_in'),
            'network_summary.required'         => __('responses.public_or_private'),
            'network_summary.in'               => __('responses.subscribed_or_unsubscribed_in'),
            'lab_summary.required'             => __('responses.required_fields'),
            'lab_summary.in'                   => __('responses.subscribed_monthly_weekly_in'),
            'challenge_summary.required'       => __('responses.required_fields'),
            'challenge_summary.in'             => __('responses.subscribed_monthly_weekly_in'),
            'challenge_recommendation.required'=> __('responses.required_fields'),
            'challenge_recommendation.in'      => __('responses.subscribed_monthly_weekly_in'),
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
