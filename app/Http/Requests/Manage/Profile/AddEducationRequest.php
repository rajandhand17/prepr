<?php

namespace App\Http\Requests\Manage\Profile;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEducationRequest extends FormRequest
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
            'university'     => 'required',
            'degree'         => 'required',
            'start_date'     => 'required|date|before:tomorrow',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'address'        => 'required',
            'description'    => 'required',
        ];
    }
    public function messages()
    {
        return[
            'user_id.required'         => __('responses.user_id_required'),
            'university.required'      => __('responses.university_required'),
            'degree.required'          => __('responses.university_required'),
            'start_date.required'      => __('responses.university_required'),
            'start_date.before_or_equal'=> __('responses.before_or_equal'),
            'end_date.required'        => __('responses.university_required'),
            'end_date.before_or_equal' => __('responses.before_or_equal'),
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
