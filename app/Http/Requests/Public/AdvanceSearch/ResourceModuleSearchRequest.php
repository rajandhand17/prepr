<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use Illuminate\Contracts\Validation\ValidationRule;

class ResourceModuleSearchRequest extends AdvanceSearchBaseFormRequest
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
            'category'   => 'array|nullable',
            'category.*' => 'numeric',
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
            'resource_module_status'      => $this->mapConstants($this->get('status'), $this->statusMap),
            'resource_module_privacy'     => $this->mapConstants($this->get('privacy'), $this->privacyMap),
            'resource_module_skills_id'   => $this->get('skill'),
            'resource_module_duration_id' => $this->get('duration'),
            'resource_module_level_id'    => $this->get('level'),
        ];
    }
}
