<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use Illuminate\Contracts\Validation\ValidationRule;

class ResourceCollectionSearchRequest extends AdvanceSearchBaseFormRequest
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
            'status'     => 'array|nullable',
            'status.*'   => 'string',
            'privacy'    => 'array|nullable',
            'privacy.*'  => 'string',
            'skill'      => 'array|nullable',
            'skill.*'    => 'numeric',
            'level'      => 'array|nullable',
            'level.*'    => 'numeric',
            'duration'   => 'array|nullable',
            'duration.*' => 'numeric',
            'sort_by'    => 'string|nullable|in:created_data_asc,created_data_desc',
        ];
    }

    public function formattedFilter(): array
    {
        return [
            'resource_collection_status'      => $this->mapConstants($this->validated('status'), $this->statusMap),
            'resource_collection_privacy'     => $this->mapConstants($this->validated('privacy'), $this->privacyMap),
            'resource_collection_skills_id'   => $this->validated('skill'),
            'resource_collection_level_id'    => $this->validated('level'),
            'resource_collection_duration_id' => $this->validated('duration'),
        ];
    }
}
