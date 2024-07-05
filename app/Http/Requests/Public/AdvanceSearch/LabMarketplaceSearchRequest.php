<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use Illuminate\Contracts\Validation\ValidationRule;

class LabMarketplaceSearchRequest extends AdvanceSearchBaseFormRequest
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
            'keyword'        => 'string|nullable',
            'status'         => 'array|nullable',
            'status.*'       => 'string',
            'privacy'        => 'array|nullable',
            'privacy.*'      => 'string',
            'skill'          => 'array|nullable',
            'skill.*'        => 'numeric',
            'level'          => 'array|nullable',
            'level.*'        => 'numeric',
            'category'       => 'array|nullable',
            'category.*'     => 'numeric',
            'organization'   => 'array|nullable',
            'organization.*' => 'numeric',
            'duration'       => 'array|nullable',
            'duration.*'     => 'numeric',
            'sort_by' => 'string|nullable|in:created_data_asc,created_data_desc'
        ];
    }

    public function formattedFilter(): array
    {
        return [
            'lab_marketplace_status'          => $this->mapConstants($this->get('status'), $this->statusMap),
            'lab_marketplace_privacy'         => $this->mapConstants($this->get('privacy'), $this->privacyMap),
            'lab_marketplace_skills_id'       => $this->get('skill'),
            'lab_marketplace_duration_id'     => $this->get('duration'),
            'lab_marketplace_level_id'        => $this->get('level'),
            'lab_marketplace_organization_id' => $this->get('organization'),
            'lab_marketplace_category_id'     => $this->get('category'),
        ];
    }
}
