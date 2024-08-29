<?php

namespace App\Http\Requests\Manage\Challenge;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateChallengeAnnouncementRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $base_rules = [
            'subject'                                   => 'required',
            'to_recipient_ids'                          => 'required|array',
            'to_recipient_ids.*'                        => 'numeric|'.Rule::exists('challenge_announcement_recipients', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'sent_by'                                   => 'required',
            'sent_by.*'                                 => 'in:email,inbox,both',
            'description'                               => 'required',
            'status'                                    => 'required',
            'status.*'                                  => 'in:send,draft,scheduled',
            'schedule_at'                               => ['required_if:status,scheduled', 'date_format:Y-m-d H:i', 'after_or_equal:'.Carbon::now()->toDateTimeString()],
        ];

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

    public function message()
    {
        return [
            'subject.required'                          => __('responses.subject_required'),
            'to_recipient_ids.required'                 => __('responses.to_recipient_required'),
            'to_recipient_ids.*.exists'                 => __('responses.to_recipient_not_found'),
            'sent_by.required'                          => __('responses.sending_mode'),
            'sent_by.in'                                => __('responses.choose_sending_mode'),
            'description.required'                      => __('responses.description_required'),
            'schedule_at.required'                      => __('responses.schedule_date_required'),
            'status.required'                           => __('responses.status_required'),
        ];
    }
}
