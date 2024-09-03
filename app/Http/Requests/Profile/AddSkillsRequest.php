<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddSkillsRequest extends FormRequest
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
            'skill_id'   => 'required|array',
            'skill_id.*' => Rule::exists('skills', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
        ];
    }

    public function messages()
    {
        return [
            'skill_id.required'      => __('responses.skill_id_required'),
            'skill_id.array'         => __('responses.array_status'),
            'skill_id.*.exists'      => __('responses.skill_id_exists'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }
}
