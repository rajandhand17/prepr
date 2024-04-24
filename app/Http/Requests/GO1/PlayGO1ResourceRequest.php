<?php

namespace App\Http\Requests\GO1;

use App\Http\Requests\BaseRequest;

class PlayGO1ResourceRequest extends BaseRequest
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
            'go1_course_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'go1_course_id.required' => __('responses.go1_course_id_required'),
        ];
    }
}
