<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNotificationRequest extends FormRequest
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
        $baseRules=[
            'communication'              =>'required|in:subscribed,unsubscribed',
            'network_summary'            =>'required|in:subscribed,unsubscribed',
            'lab_summary'                =>'required|in:unsubscribed,monthly, weekly',
            'challenge_summary'          =>'required|in:unsubscribed,monthly, weekly',
            'challenge_recommendation'   =>'required|in:unsubscribed,monthly, weekly',
        ];
        return $baseRules;
    }
    public function messages()
    {
        return [
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
