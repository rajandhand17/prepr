<?php

namespace App\Http\Requests\Project;

use App\Services\ProjectService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateProjectRequest extends FormRequest
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
        $project = ProjectService::getProjectBasedOnSlug(request()->route('slug'));
        if (!$project) {
            throw new NotFoundHttpException();
        }

        $base_rules = [
            'title'                     => 'required|max:255|unique:projects,title,'.$project->id,
            'description'               => 'required',
            'is_view_enabled'           => 'required|in:yes,no',
            'is_download_enabled'       => 'required|in:yes,no',
            'media_type'                => 'required|in:image,embedded',
            'privacy'                   => 'required|in:public,private',
            'cover_media'               => 'nullable|mimes:jpeg,jpg,png,webp|max:1024',
            'lab_id'                    => 'nullable|exists:labs,id',
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
            'lab_id.exists'                 => __('responses.lab_not_exists'),
        ];
    }
}
