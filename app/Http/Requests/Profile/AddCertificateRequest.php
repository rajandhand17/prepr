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
            'company'       => 'required',
            'name'          => 'required',
            'start_date'    => 'required|date|before_or_equal:'.Carbon::now()->toDateTimeString(),
            'end_date'      => 'required|date|after:start_date',
            'description'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'      => __('responses.user_id_required'),
            'company.required'      => __('responses.company_required'),
            'name.required'         => __('responses.name_required'),
            'start_date.required'   => __('responses.start_date_required'),
            'end_date.required'     => __('responses.end_date_required'),
            'description.required'  => __('responses.description_required'),
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
