<?php

namespace App\Http\Requests\Career;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddJobPinnedRequest extends FormRequest
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
            'job_id'     => 'required|'.Rule::exists('job_titles', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'pinned'     => 'required|in:yes,no',
        ];
    }

    public function messages()
    {
        return [
            'skill_id.required'      => __('responses.job_id_required'),
            'skill_id.exists'        => __('responses.job_id_exists'),
            'pinned.in'              => __('responses.choose_yes_no'),
            'pinned.required'        => __('responses.required_field'),
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
