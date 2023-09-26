<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateResourceModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $base_rules = [
            'title'                  => 'required|unique:resource_modules,title',
            'organization_id'        => 'required|exists:organizations,uuid',
            'description'            => 'required',
            'cover_image'            => 'nullable|mimes:jpeg,jpg,png,webp|max:1024',
            'privacy'                => 'required|in:yes,no',
            'status'                 => 'required|in:draft,published,archive',
            'is_global'              => 'required|in:yes,no'

        ];

        return $base_rules;
    }

    public function messages(){
        return [
            'title.required'                 => __('responses.title_required'),
            'title.unique'                   => __('responses.lab_program_title_unique'),
            'description.required'           => __('responses.description_required'),
            'organization_id.required'       => __('responses.organization_id_required'),
            'organization_id.exists'         => __('responses.organization_not_exists'),
            'privacy.required'               => __('responses.privacy_required'),
            'privacy.in'                     => __('responses.choose_yes_no'),
            'status.required'                => __('responses.status_required'),
            'status.in'                      => __('responses.status_in'),
            'is_global.required'             => __('responses.choose_yes_no'),
            'cover_image.mimes'              => __('responses.cover_image_type'),
            'cover_image.max'                => __('responses.cover_image_max'),
        ];
    }
}
