<?php

namespace App\Http\Requests\Profile;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddExperienceRequest extends FormRequest
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
            'company'      => 'required|array',
            'company.*'    => 'max:255',
            'description'  => 'required|array',
            'start_date'   => 'required|array',
            'start_date.*' => 'before_or_equal:'.Carbon::now()->subYears(10)->toDateTimeString(),
            'end_date'     => 'required|array',
            'end_date.*'   => 'after_or_equal:start_date.*',
            'position'     => 'required|array',
            'address'      => 'required|array',
            'state'        => 'required|array',
            'country'      => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'company.required'         => __('responses.company_required'),
            'company.array'            => __('responses.array_status'),
            'company.*.max'            => __('response.max_content_255'),
            'description.required'     => __('response.description_required'),
            'description.array'        => __('response.array_status'),
            'position.required'        => __('response.position_required'),
            'position.array'           => __('response.array_status'),
            'start_date.required'      => __('response.start_date_required'),
            'start_date.array'         => __('response.array_status'),
            'start_date.*.before_or_equal'=> __('response.before_or_equal'),
            'end_date.required'        => __('response.end_date_required'),
            'end_date.array'           => __('response.array_status'),
            'end_date.*.after_or_equal'=> __('response.end_date_required'),
            'address.required'         => __('response.address_required'),
            'address.array'            => __('response.array_status'),
            'state.required'           => __('response.state_required'),
            'state.array'              => __('response.array_status'),
            'country.required'         => __('response.country_required'),
            'country.array'            => __('response.array_status'),
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
