<?php

namespace App\Http\Requests\Manage\LabProgram;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateLabProgramRequest extends FormRequest
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
        $achievement_en_switch = $this->request->get('is_achievement_enabled');
        $base_rules = [
            'title'                   => 'required|unique:lab_programs,title',
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

        ];
        if ($achievement_en_switch == 'Yes' || $achievement_en_switch == 'yes') {
            $base_rules['achievement_name'] = 'required';
            $base_rules['achievement_points'] = 'required';
            $base_rules['achievement_image'] = 'required|mimes:jpeg,jpg,png,webp|max:1024';
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
        ];
    }
}
