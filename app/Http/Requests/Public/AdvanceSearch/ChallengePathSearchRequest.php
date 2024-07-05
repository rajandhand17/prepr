<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use Illuminate\Contracts\Validation\ValidationRule;

class ChallengePathSearchRequest extends AdvanceSearchBaseFormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => 'string|nullable',
            'status' => 'array|nullable',
            'status.*' => 'string',
            'category' => 'array|nullable',
            'category.*' => 'numeric',
            'privacy' => 'array|nullable',
            'privacy.*' => 'string',
            'skill' => 'array|nullable',
            'skill.*' => 'numeric',
            'level' => 'array|nullable',
            'level.*' => 'numeric',
            'duration' => 'array|nullable',
            'duration.*' => 'numeric',
            'sort_by' => 'string|nullable|in:created_data_asc,created_data_desc'
        ];
    }

    public function formattedFilter(): array
    {
        return [
            'challenge_path_status' => $this->mapConstants($this->get('status'), $this->statusMap),
            'challenge_path_privacy' => $this->mapConstants($this->get('privacy'), $this->privacyMap),
            'challenge_path_skills_id' => $this->get('skill'),
            'challenge_path_duration_id' => $this->get('duration'),
            'challenge_path_level_id' => $this->get('level'),
            'challenge_path_category_id' => $this->get('category'),
        ];
    }
}
