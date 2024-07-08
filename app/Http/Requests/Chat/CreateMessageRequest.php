<?php

namespace App\Http\Requests\Chat;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateMessageRequest extends BaseRequest
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
            'message'      => 'required_without:attachment',
            'attachment'   => 'required_without:message|array',
            'attachment.*' => 'file|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'message.required_without'    => __('responses.message_without_attachment'),
            'attachment.required_without' => __('responses.attachment_without_message'),
            'attachment.*.file'           => __('responses.chat_file'),
            'attachment.*.max'            => __('responses.chat_max_file_size'),
        ];
    }
}
