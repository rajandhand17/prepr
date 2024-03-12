<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProjectRequest extends FormRequest
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
            'title'                     => 'required|unique:projects,title',
            'description'               => 'required',
            'is_view_enabled'           => 'required|in:yes,no',
            'is_download_enabled'       => 'required|in:yes,no',
            'media_type'                => 'required|in:image,embedded',
            'privacy'                   => 'required|in:public,private',
            'cover_media'               => 'nullable|mimes:jpeg,jpg,png,webp|max:1024',
            'challenge_id'              => 'required|exists:challenges,uuid',
            'lab_id'                    => 'nullable|exists:labs,uuid',
        ];

        return $base_rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }

    public function messages()
    {
        return [
            'title.required'                => __('responses.title_required'),
            'title.unique'                  => __('responses.project_title_unique'),
            'description.required'          => __('responses.description_required'),
            'is_view_enabled.in'            => __('responses.choose_yes_no'),
            'is_download_enabled.in'        => __('responses.choose_yes_no'),
            'media_type.in'                 => __('responses.choose_image_embedded'),
            'privacy.in'                    => __('responses.choose_status'),
            'challenge_id.required'         => __('responses.challenge_id_field_required'),
            'challenge_id.exists'           => __('responses.challenge_not_exists'),
            'lab_id.exists'                 => __('responses.lab_not_exists'),
        ];
    }
}
