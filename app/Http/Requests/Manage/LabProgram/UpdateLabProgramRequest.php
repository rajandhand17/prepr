<?php

namespace App\Http\Requests\Manage\LabProgram;

use App\Services\Manage\LabProgramService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use League\Container\Exception\NotFoundException;

class UpdateLabProgramRequest extends FormRequest
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
        $labProgram = LabProgramService::getLabProgramBasedOnSlug(request()->route('slug'));
        if (!$labProgram) {
            throw new NotFoundException();
        }
        $achievement_en_switch = $this->request->get('is_achievement_enabled');
        $base_rules = [
            'title'                   => 'required|max:255|unique:lab_programs,title,'.$labProgram->id,
            'description'             => 'required',
            'category_id'             => 'required|exists:categories,id',
            'level_id'                => 'required|exists:levels,id',
            'duration_id'             => 'required|exists:durations,id',
            'lab_ids'                 => 'required|array',
            'lab_ids.*'               => 'exists:labs,uuid',
            'skills'                  => 'required|array',
            'skills.*'                => 'numeric|exists:skills,id',
            'is_sequential'           => 'in:yes,no',
            'privacy'                 => 'in:yes,no',
            'is_achievement_enabled'  => 'in:yes,no',
            'type'                    => 'array',
            'type.*'                  => 'in:assess,onboard,engage,grow',
            'mode'                    => 'array',
            'mode.*'                  => 'in:team,individual',
            'media_type'              => 'in:image,embedded',
        ];
        if ($achievement_en_switch == 'Yes' || $achievement_en_switch == 'yes') {
            $base_rules['achievement_name'] = 'required';
            $base_rules['achievement_points'] = 'required';
            $base_rules['achievement_image'] = 'required|mimes:jpeg,jpg,png,webp|max:1024';
        }

        // Lab Program cover image validation
        if ($this->has('media_type') && $this->input('media_type') == 'image') {
            if ($labProgram->media != null && $labProgram->getRawOriginal('media') == 'default_images/lab-program.webp') {
                if ($this->hasFile('media') && $this->file('media')->isValid()) {
                    $base_rules['media'] = [
                        'required',
                        'mimes:jpeg,jpg,png,webp',
                        'max:5120',
                        function ($attribute, $value, $fail) {
                            if ($value && $value->isValid()) {
                                $image = getimagesize($value);
                                if ($image && ($image[0] < 625 || $image[1] < 355)) {
                                    $fail(''.$attribute.' must be at least 625x355 pixels.');
                                }
                            }
                        },
                    ];
                }
            }
        }

        if ($this->has('media_type') && $this->input('media_type') == 'embedded') {
            $regexYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexNoCookieYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube-nocookie.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexVimeo = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/player.vimeo.com\/(?:\b|_).*?(?:\b|_)iframe>/';

            $cover_embedded = $this->input('media');
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

            $base_rules['media'] = [
                'required',
                function ($attribute, $value, $fail) use ($isValid) {
                    if ($isValid === 0) {
                        $fail($attribute.' must contain exactly one valid YouTube or Vimeo iframe.');
                    } elseif ($isValid > 1) {
                        $fail($attribute.' must not contain more than one valid YouTube or Vimeo iframe.');
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
            'title.required'                 => __('responses.title_required'),
            'title.unique'                   => __('responses.lab_program_title_unique'),
            'description.required'           => __('responses.description_required'),
            'category_id.required'           => __('responses.category_id_required'),
            'category_id.exists'             => __('responses.category_not_found'),
            'level_id.required'              => __('responses.level_id_required'),
            'level_id.exists'                => __('responses.level_id_exists'),
            'duration_id.required'           => __('responses.duration_id_required'),
            'duration_id.exists'             => __('responses.duration_id_exists'),
            'lab_id.required'                => __('responses.lab_id_required'),
            'lab_id.exists'                  => __('responses.lab_id_exists'),
            'lab_id.array'                   => __('responses.lab_id_array'),
            'is_sequential.in'               => __('responses.choose_yes_no'),
            'is_achievement_enabled.in'      => __('responses.choose_yes_no'),
            'privacy.in'                     => __('responses.choose_yes_no'),
            'achievement_name.required'      => __('responses.achievement_name_required'),
            'achievement_points.required'    => __('responses.achievement_points_required'),
            'achievement_condition.required' => __('responses.achievement_conditions_required'),
            'achievement_image.required'     => __('responses.achievement_image_required'),
            'achievement_image.mimes'        => __('responses.mimes_image'),
            'achievement_image.max'          => __('responses.mimes_image_max'),
            'type.required'                  => __('responses.type_required'),
            'type.in'                        => __('responses.type_in'),
            'mode.required'                  => __('responses.mode_required'),
            'mode.in'                        => __('responses.resource_mode_in'),
            'media_type.in'                  => __('responses.choose_image_embedded'),
        ];
    }
}
