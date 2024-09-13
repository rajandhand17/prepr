<?php

namespace App\Http\Requests\Chat;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class CreateConversationRequest extends BaseRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'usernames' => 'required|'.
            Rule::exists('users', 'username')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'type'      => 'required|in:announcement,message',
        ];
    }

    public function messages()
    {
        return[
            'usernames.required' => __('responses.conversation_users_required'),
            'type.required'      => __('responses.conversation_type_required'),
            'usernames.exists'   => __('responses.conversation_user_exists'),
            'type.in'            => __('responses.type_in_announcement_or_message'),
        ];
    }
}
