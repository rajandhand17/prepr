<?php

namespace App\Http\Requests\Manage\Profile;

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
            'user_id'      => 'required',
            'title'        => 'required|numeric',
            'name'         => 'required',
            'description'  => 'required',
            'patent_date'  => 'required|date|before_or_equal:'.Carbon::now()->toDateTimeString(),
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'user_id.required'      => __('responses.user_id_required'),
            'title.required'        => __('responses.title_required'),
            'name.required'         => __('responses.name_required'),
            'description.required'  => __('responses.description_required'),
            'patent_date.required'  => __('responses.patent_date'),
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
