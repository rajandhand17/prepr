<?php

namespace App\Http\Requests\Manage\ChannelApi;

use App\Http\Requests\BaseRequest;

class AssignUserToLabRequest extends BaseRequest
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
        return [
            'user'              => 'required|array',
            'user.*.id'         => 'required|integer',
            'user.*.first_name' => 'required|string',
            'user.*.last_name'  => 'required|string',
            'user.*.user_name'  => 'required|string',
            'user.*.telephone'  => 'required|string',
            'user.*.type'       => 'required|string',
            'user.*.email'      => 'required|email',
            'user.*.status'     => 'required|string',
            'lab_id'            => 'required',
        ];
    }

    public function messages()
    {
        return [
            'user.required'              => __('responses.channel_api_user_required'),
            'user.array'                 => __('responses.channel_api_user_array'),
            'user.*.id.required'         => __('responses.channel_api_user_id_required'),
            'user.*.id.integer'          => __('responses.channel_api_user_id_integer'),
            'user.*.first_name.required' => __('responses.channel_api_user_first_name_required'),
            'user.*.first_name.string'   => __('responses.channel_api_user_first_name_string'),
            'user.*.last_name.required'  => __('responses.channel_api_user_last_name_required'),
            'user.*.last_name.string'    => __('responses.channel_api_user_last_name_string'),
            'user.*.user_name.required'  => __('responses.channel_api_user_username_required'),
            'user.*.user_name.string'    => __('responses.channel_api_user_username_string'),
            'user.*.telephone.required'  => __('responses.channel_api_user_telephone_required'),
            'user.*.telephone.string'    => __('responses.channel_api_user_telephone_string'),
            'user.*.type.required'       => __('responses.channel_api_user_type_required'),
            'user.*.type.string'         => __('responses.channel_api_user_type_string'),
            'user.*.email.required'      => __('responses.channel_api_user_email_required'),
            'user.*.email.email'         => __('responses.channel_api_user_email_email'),
            'user.*.status.required'     => __('responses.channel_api_status_required'),
            'user.*.status.string'       => __('responses.channel_api_status_string'),
            'lab_id.required'            => __('responses.channel_api_lab_id_required'),
        ];
    }
}
