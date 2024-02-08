<?php

namespace App\Http\Requests\Chat;


use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateChatRequest extends BaseRequest
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
            'message' => 'required_without:attachment',
            'attachment' => 'required_without:message'
        ];
    }
}
