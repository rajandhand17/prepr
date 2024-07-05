<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use Illuminate\Contracts\Validation\ValidationRule;

class LabProgramSearchRequest extends AdvanceSearchBaseFormRequest
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
            'keyword'    => 'string|nullable',
            'status'     => 'array',
            'status.*'   => 'string',
            'category'   => 'array',
            'category.*' => 'numeric',
            'privacy'    => 'array',
            'privacy.*'  => 'string',
            'skill'      => 'array',
            'skill.*'    => 'numeric',
            'level'      => 'array',
            'level.*'    => 'numeric',
            'duration'   => 'array',
            'duration.*' => 'numeric',
            'sort_by'    => 'string|nullable|in:created_data_asc,created_data_desc',
        ];
    }

    /**
     * @return array
     */
    public function formattedFilter(): array
    {
        return [
            'lab_program_status'      => $this->mapConstants($this->get('status'), $this->statusMap),
            'lab_program_category_id' => $this->get('category'),
            'lab_program_privacy'     => $this->mapConstants($this->get('privacy'), $this->privacyMap),
            'lab_program_skills_id'   => $this->get('skill'),
            'lab_program_level_id'    => $this->get('level'),
            'lab_program_duration_id' => $this->get('duration'),
        ];
    }
}
