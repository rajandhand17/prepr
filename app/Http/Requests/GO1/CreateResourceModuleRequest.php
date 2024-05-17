<?php

namespace App\Http\Requests\GO1;

use App\Http\Requests\BaseRequest;

class CreateResourceModuleRequest extends BaseRequest
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
            'go1_course.id'    => 'required',
            'go1_course.title' => 'required|unique:resource_modules,title',
        ];
    }

    public function messages(): array
    {
        return [
            'go1_course.id.required'    => __('responses.go1_course_id_required'),
            'go1_course.title.required' => __('responses.go1_course_title_required'),
            'go1_course.title.unique'   => __('responses.go1_course_title_unique'),
        ];
    }
}
