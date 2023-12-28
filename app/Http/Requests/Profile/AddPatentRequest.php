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
            'title'        => 'required|array',
            'title.*'      => 'max:255|string',
            'name'         => 'required|array',
            'name.*'       => 'max:255|string',
            'description'  => 'required|array',
            'patent_date'  => 'required|array',
            'patent_date.*'=> 'before_or_equal:'.Carbon::now()->subYears(10)->toDateTimeString(),
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'title.required'                  => __('responses.title_required'),
            'title.array'                     => __('responses.status_array'),
            'title.*.max'                     => __('responses.status_array'),
            'title.*.string'                  => __('responses.string_data_allowed'),
            'name.*.max'                      => __('responses.status_array'),
            'name.*.string'                   => __('responses.string_data_allowed'),
            'name.required'                   => __('responses.name_required'),
            'name.array'                      => __('responses.status_array'),
            'description.required'            => __('responses.description_required'),
            'description.array'               => __('responses.status_array'),
            'parent_date.required'            => __('responses.parent_date_required'),
            'parent_date.array'               => __('responses.status_array'),
            'parent_date.*.before_or_equal'   => __('responses.before_or_equal'),
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
