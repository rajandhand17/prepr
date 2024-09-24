<?php

namespace App\Http\Requests\Profile;

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
        $return = [
            'university'       => 'required|array',
            'university.*'     => 'max:255',
            'degree'           => 'required|array',
            'degree.*'         => 'max:255',
            'start_date'       => 'nullable|array',
            'start_date.*'     => 'before_or_equal:'.Carbon::now()->toDateTimeString(),
            'end_date'         => 'nullable|array',
            'end_date.*'       => 'after_or_equal:start_date.*',
            'address'          => 'nullable|array',
            'state'            => 'nullable|array',
            'country'          => 'nullable|array',
            'description'      => 'nullable|array',
        ];
        if ($this->enrollment_status == 'yes') {
            $return = [
                'student_number'     => 'required',
                'current_program'    => 'required',
                'current_degree'     => 'required',
                'current_institution'=> 'required',
                'institution_type'   => 'required',
                'current_year'       => 'required',
            ];
        }

        return $return;
    }

    public function messages()
    {
        return[
            'university.array'            => __('responses.array_status'),
            'university.required'         => __('responses.university_required'),
            'university.*.max'            => __('responses.max_content_255'),
            'degree.required'             => __('responses.degree_required'),
            'degree.array'                => __('responses.array_status'),
            'degree.*.max'                => __('responses.max_content_255'),
            'start_date.required'         => __('responses.start_date_required'),
            'start_date.array'            => __('responses.array_status'),
            'start_date.*.before'         => __('responses.before_or_equal'),
            'end_date.required'           => __('responses.end_date_required'),
            'end_date.array'              => __('responses.array_status'),
            'end_date.*.after_or_equal'   => __('responses.end_date_required'),
            'address.required'            => __('responses.required_field'),
            'address.array'               => __('responses.array_status'),
            'state.required'              => __('responses.required_field'),
            'state.array'                 => __('responses.array_status'),
            'country.required'            => __('responses.required_field'),
            'country.array'               => __('responses.array_status'),
            'description.required'        => __('responses.required_field'),
            'description.array'           => __('responses.array_status'),
            'student_number.required'     => __('responses.required_field'),
            'current_program.required'    => __('responses.required_field'),
            'current_degree.required'     => __('responses.required_field'),
            'current_institution.required'=> __('responses.required_field'),
            'institution_type.required'   => __('responses.required_field'),
            'current_year.required'       => __('responses.required_field'),

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
