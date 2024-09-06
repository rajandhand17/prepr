<?php

namespace App\Http\Requests\Public\Notification;

use App\Http\Requests\BaseRequest;
use App\Notifications\NotificationTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class NotificationRequest extends BaseRequest
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
            'type' => ['nullable', Rule::in([
                NotificationTypes::LEARNING_POINT,
                NotificationTypes::FRIEND_REQUEST,
                NotificationTypes::CHALLENGE,
                NotificationTypes::ORGANIZATION,
                NotificationTypes::LAB,
            ])],
        ];
    }
}
