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
        $base_rules=[];
        if($activity=='account'){
            $base_rules=[
                'first_name'               => 'required_if:request_type,account|string',
                'last_name'                => 'required_if:request_type,account|string',
                'username'                 => 'required_if:request_type,account',
                'email'                    => 'required_if:request_type,account|email',
                'phone_number'             => 'required_if:request_type,account',
                'preferred_timezone'       => 'required_if:request_type,account',
                'preferred_language'       => 'required_if:request_type,account',
                'two_factor_verification'  => 'required_if:request_type,account|in:true,false',

            ];
        }elseif($activity=='privacy'){
            $base_rules=[
                'profile_visibility'       => 'required_if:request_type,privacy|in:public,private,signed-in',
                'project_visibility'       => 'required_if:request_type,privacy|in:public,private',
                'friend_request'           => 'required_if:request_type,privacy|in:any-one,no-one',
            ];
        }elseif($activity=='password'){
            $base_rules=[
                'password'                 => 'required_if:request_type,password|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'password_confirmation'    => 'required_if:request_type,password|same:password',
            ];
        }elseif($activity=='notification'){
            $base_rules=[
                'communication'            => 'required|in:subscribed,unsubscribed',
                'network_summary'          => 'required|in:subscribed,unsubscribed',
                'lab_summary'              => 'required|in:unsubscribed,monthly,weekly',
                'challenge_summary'        => 'required|in:unsubscribed,monthly,weekly',
                'challenge_recommendation' => 'required|in:unsubscribed,monthly,weekly',
            ];
        }
        return $base_rules;

    }

    public function messages()
    {
        return [
            'profile_visibility.required'     => __('responses.required_fields'),
            'profile_visibility.in'           => __('responses.profile_privacy_in'),
            'project_visibility.required'     => __('responses.public_or_private'),
            'project_visibility.in'           => __('responses.public_or_private'),
            'friend_request.required'         => __('responses.required_fields'),
            'friend_request.in'               => __('responses.any_or_no_one'),
            'first_name.required'             => __('responses.first_name_field_required'),
            'last_name.required'              => __('responses.last_name_field_required'),
            'purpose.numeric'                 => __('responses.numeric_allowed_only'),
            'username.required'               => __('responses.required_field'),
            'email.required'                  => __('responses.email_field_required'),
            'email.email'                     => __('responses.valid_email_pattern'),
            'phone_number.required'           => __('responses.required_field'),
            'preferred_timezone.required'     => __('responses.required_field'),
            'preferred_language.required'     => __('responses.required_field'),
            'two_factor_verification.required'=> __('responses.required_field'),
            'two_factor_verification.in'      => __('responses.true_or_false'),
            'password.required'              => __('responses.password_required_field'),
            'password.min'                   => __('responses.min_content_6'),
            'password_confirmation.required' => __('responses.password_confirmation_required_field'),
            'password_confirmation.same'     => __('responses.match_confirmed_password'),
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
