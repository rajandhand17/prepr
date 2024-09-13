<?php

namespace App\Http\Requests\Project;

use App\Models\ChallengeAssessmentCriteria;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddProjectAssessmentRequest extends FormRequest
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
            'criteria_id'       => 'array|required',
            'criteria_id.*'     => 'numeric|'.Rule::exists('challenge_assessment_criterias', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'score'             => 'array|required',
            'score.*'           => 'numeric',
            'comment'           => 'array',
            'status'            => 'required|in:draft,published',
            'criteria_comment'  => 'required|string|min:1',
        ];

        $criteriaIds = $this->input('criteria_id');
        $scores = $this->input('score');
        $comments = $this->input('comment');

        foreach ($criteriaIds as $key => $criteriaId) {
            $base_rules["score.$key"] = [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($criteriaId, $scores, $key) {
                    $checkCriteria = ChallengeAssessmentCriteria::find($criteriaId);
                    if ($scores[$key] > $checkCriteria->score) {
                        $fail("The score provided for criteria_id $criteriaId is invalid.");
                    }
                },
            ];
        }

        // foreach ($criteriaIds as $key => $criteriaId) {
        //     $base_rules["comment.$key"] = [
        //         'string',
        //         function ($attribute, $value, $fail) use ($criteriaId, $comments, $key) {
        //             if (!isset($comments[$key]) || empty($comments[$key])) {
        //                 $fail("The comment for criteria_id $criteriaId is missing or empty.");
        //             }
        //         },
        //     ];
        // }

        return $base_rules;
    }

    public function messages()
    {
        return [
            'criteria_id.exists'        => __('responses.external_link_ids_not_exists'),
            'criteria_id.array'         => __('responses.external_link_ids_array'),
            'criteria_id.numeric'       => __('responses.external_link_ids_numeric'),
            'score.*'                   => ':attribute'.' '.__('responses.project_score_exceeds'),
            'comment.*'                 => ':attribute'.' '.__('responses.project_score_comment'),
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
