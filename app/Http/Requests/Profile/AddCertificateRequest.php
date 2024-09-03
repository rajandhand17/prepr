<?php

namespace App\Http\Requests\Profile;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddCertificateRequest extends FormRequest
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
            'company'         => 'required|array',
            'company.*'       => 'max:255|string',
            'name'            => 'required|array',
            'name.*'          => 'max:255|string',
            'start_date'      => 'required|array',
            'start_date.*'    => 'before_or_equal:'.Carbon::now()->toDateTimeString(),
            'end_date'        => 'required|array',
            'end_date.*'      => 'after_or_equal:start_date.*',
            'description'     => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'company.required'                  => __('responses.company_required'),
            'company.array'                     => __('responses.array_status'),
            'company.*.max'                     => __('responses.max_content_255'),
            'company.*.string'                  => __('responses.string_data_allowed'),
            'name.required'                     => __('responses.name_required'),
            'name.array'                        => __('responses.array_status'),
            'name.*.max'                        => __('responses.max_content_255'),
            'name.*.string'                     => __('responses.string_data_allowed'),
            'start_date.required'               => __('responses.start_date_required'),
            'start_date.array'                  => __('responses.array_status'),
            'start_date.*.before_or_equal'      => __('responses.before_or_equal'),
            'end_date.required'                 => __('responses.end_date_required'),
            'end_date.array'                    => __('responses.array_status'),
            'end_date.*.after_or_equal'         => __('responses.after_start'),
            'description.required'              => __('responses.description_required'),
            'description.array'                 => __('responses.array_status'),
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
