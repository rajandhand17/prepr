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
        $rules = [
            'skill_id'   => 'required|array',
            'skill_id.*' => Rule::exists('skills', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
        ];

        if ($this->has('pinned')) {
            $rules['pinned'] = ['array', function ($attribute, $value, $fail) {
                // Check if the array has more than 3 elements with value 1
                $countOnes = count(array_filter($value, fn ($pinned_item) => $pinned_item == 1));

                if ($countOnes > 3) {
                    $fail('You can only add 3 skills in featured skills.');
                }
            }];
        }

        return $rules;
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
