<?php

namespace App\Http\Requests\Manage\Profile;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddPersonalDetailRequest extends FormRequest
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
        $base_rules = [
            'user_id'       => 'required|unique:user_personal_details,user_id',
            'age'           => 'required',
            'about'         => 'required',
            'purpose'       => 'required',
            'gender'        => 'required',
            'date_of_birth' => 'required|date|before_or_equal:' . Carbon::now()->toDateTimeString(),
        ];
        return $base_rules;
    }

    public function messages()
    {
        return [
            'user_id.required'      => __('responses.user_id_required'),
            'age.required'          => __('responses.age_required'),
            'about.required'        => __('responses.about_required'),
            'purpose.required'      => __('responses.purpose_required'),
            'gender.required'       => __('responses.gender_required'),
            'date_of_birth.required'=> __('responses.user_date_of_birth'),
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
