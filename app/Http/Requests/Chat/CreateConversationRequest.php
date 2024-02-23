<?php

namespace App\Http\Requests\Chat;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

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
            'users' => 'required|exists:users,id',
            'type' => 'required|in:announcement,message'
        ];
    }
    public function messages()
    {
        return[
            'users.required' => __('responses.conversation_users_required'),
            'type.required'   => __('responses.conversation_type_required'),
            "users.exists" => __("responses.conversation_user_exists"),
            "type.in" => __("responses.type_in_announcement_or_message")
        ];
    }
}
