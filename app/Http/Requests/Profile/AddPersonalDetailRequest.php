<?php

namespace App\Http\Requests\Profile;

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
            'name'              => 'required|string',
            'gender'            => 'required|in:male,female,other,decline_to_answer',
            'purpose'           => 'numeric|between:0,12',
            'date_of_birth'     => 'date|before_or_equal:'.Carbon::now()->toDateTimeString(),
            'recent_immigrant'  => 'in:true,false',
            'indigenous_group'  => 'in:true,false',
            'visible_minority'  => 'in:true,false',
            'disability'        => 'in:true,false',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'name.required'                 => __('responses.name_required'),
            'purpose.numeric'               => __('responses.numeric_allowed_only'),
            'purpose.between'               => __('responses.between_allowed_purpose'),
            'gender.required'               => __('responses.gender_required'),
            'gender.in'                     => __('responses.gender_between'),
            'date_of_birth.required'        => __('responses.user_date_of_birth'),
            'date_of_birth.before_or_equal' => __('responses.date_of_birth_date'),
            'recent_immigrant.in'           => __('responses.true_or_false'),
            'indigenous_group.in'           => __('responses.true_or_false'),
            'visible_minority.in'           => __('responses.true_or_false'),
            'disability.in'                 => __('responses.true_or_false'),
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
