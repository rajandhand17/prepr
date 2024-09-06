<?php

namespace App\Http\Requests\Public\Notification;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class NotificationMarkAsRequest extends BaseRequest
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
            'notification_ids' => ['array', 'nullable', Rule::exists('notifications', 'id')],
        ];
    }
}
