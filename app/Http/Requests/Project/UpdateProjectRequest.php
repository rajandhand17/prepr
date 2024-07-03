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
            'media_type'                => 'nullable|in:image,embedded,none',
            'privacy'                   => 'required|in:public,private',
            'lab_id'                    => 'nullable|exists:labs,uuid',
        ];

        if ($this->has('media_type') && $this->input('media_type') == 'image') {
            $base_rules['cover_media'] = [
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ];
        }

        if ($this->has('media_type') && $this->input('media_type') == 'embedded') {
            $regexYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexNoCookieYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube-nocookie.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexVimeo = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/player.vimeo.com\/(?:\b|_).*?(?:\b|_)iframe>/';

            $cover_embedded = $this->input('cover_media');
            $isValid = 0;

            // Check for YouTube iframe
            preg_match_all($regexYoutube, $cover_embedded, $matchesYoutube);
            $isValid += count($matchesYoutube[0]);

            // Check for YouTube no-cookie iframe
            preg_match_all($regexNoCookieYoutube, $cover_embedded, $matchesNoCookieYoutube);
            $isValid += count($matchesNoCookieYoutube[0]);

            // Check for Vimeo iframe
            preg_match_all($regexVimeo, $cover_embedded, $matchesVimeo);
            $isValid += count($matchesVimeo[0]);

            $base_rules['cover_media'] = [
                'required',
                function ($attribute, $value, $fail) use ($isValid) {
                    if ($isValid === 0) {
                        $fail($attribute . ' must contain exactly one valid YouTube or Vimeo iframe.');
                    } elseif ($isValid > 1) {
                        $fail($attribute . ' must not contain more than one valid YouTube or Vimeo iframe.');
                    }
                },
            ];
        }

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
