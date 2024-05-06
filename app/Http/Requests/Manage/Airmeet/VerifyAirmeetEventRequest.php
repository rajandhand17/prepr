<?php

namespace App\Http\Requests\Manage\Airmeet;

use App\Http\Requests\BaseRequest;

class VerifyAirmeetEventRequest extends BaseRequest
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
            'event_id' => ['required', 'string'],
        ];
    }
}
