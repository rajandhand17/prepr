<?php

namespace App\Http\Requests\Profile;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddPatentRequest extends FormRequest
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
            'company'      => 'required|array',
            'company.*'    => 'max:255|string',
            'name'         => 'required|array',
            'name.*'       => 'max:255|string',
            'description'  => 'nullable|array',
            'patent_date'  => 'nullable|array',
            'patent_date.*'=> 'before_or_equal:'.Carbon::now()->toDateTimeString(),
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'company.required'                  => __('responses.company_required'),
            'company.array'                     => __('responses.status_array'),
            'company.*.max'                     => __('responses.max_content_255'),
            'company.*.string'                  => __('responses.string_data_allowed'),
            'name.*.max'                        => __('responses.max_content_255'),
            'name.*.string'                     => __('responses.string_data_allowed'),
            'name.required'                     => __('responses.name_required'),
            'name.array'                        => __('responses.status_array'),
            'description.required'              => __('responses.description_required'),
            'description.array'                 => __('responses.status_array'),
            'parent_date.required'              => __('responses.parent_date_required'),
            'parent_date.array'                 => __('responses.status_array'),
            'parent_date.*.before_or_equal'     => __('responses.before_or_equal'),
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
