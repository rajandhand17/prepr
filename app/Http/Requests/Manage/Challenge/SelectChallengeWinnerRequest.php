<?php

namespace App\Http\Requests\Manage\Challenge;

use App\Models\ChallengeAchievement;
use App\Services\Manage\ChallengeService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Intervention\Image\Exception\NotFoundException;

class SelectChallengeWinnerRequest extends FormRequest
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
        $challengeId = ChallengeService::getChallengeBasedOnSlug(request()->route('slug'))->id;
        if (!$challengeId) {
            throw new NotFoundException();
        }
        $requiredWinnerCount = ChallengeAchievement::where(['challenge_id' => $challengeId, 'achievement_type' => '1'])->count();

        $base_rules = [
            'project_id'                => ['array', 'required', 'size:'.$requiredWinnerCount, 'distinct'],
            'project_id.*'              => [Rule::exists('projects', 'uuid')->where(function ($query) {
                $query->whereNull('deleted_at')->where('is_submitted', '!=', '0');
            }),
            ],
            'winner_achievement_id'     => ['array', 'required', 'size:'.$requiredWinnerCount, 'distinct'],
            'winner_achievement_id.*'   => [
                'required', 'integer',
                Rule::exists('challenge_achievements', 'id')->where(function ($query) {
                    $query->where('achievement_type', '1');
                }),
            ],
        ];

        return $base_rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $winnerAchievementIds = $this->input('winner_achievement_id', []);
            $uniqueWinnerAchievementIds = array_unique($winnerAchievementIds);

            if (count($winnerAchievementIds) !== count($uniqueWinnerAchievementIds)) {
                $validator->errors()->add('winner_achievement_id', __('responses.challenge_winner_id_unique'));
            }

            $projectIds = $this->input('project_id', []);
            $uniqueProjectIds = array_unique($projectIds);

            if (count($projectIds) !== count($uniqueProjectIds)) {
                $validator->errors()->add('project_id', __('responses.project_id_unique'));
            }

            if (count($winnerAchievementIds) !== count($projectIds)) {
                $validator->errors()->add('winner_achievement_id', __('responses.challenge_winner_id_match_number'));
                $validator->errors()->add('project_id', __('responses.project_id_match_number'));
            }
        });
    }

    public function messages()
    {
        return [
            'project_id.exists'        => __('responses.project_ids_not_exists'),
            'project_id.array'         => __('responses.project_ids_array'),
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
