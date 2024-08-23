<?php

namespace App\Http\Requests\Manage\LabMarketPlace;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class LabMarketplaceRequest extends FormRequest
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
            'organization_id'=> 'required|'.Rule::exists('organizations', 'uuid')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
        ];
    }

    public function messages()
    {
        return[
            'organization_id.required'       => __('responses.organization_id'),
            'organization_id.exists'         => __('responses.organization_exists'),

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
