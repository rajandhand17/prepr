<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use Illuminate\Contracts\Validation\ValidationRule;

class ProjectSearchRequest extends AdvanceSearchBaseFormRequest
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
            'category'   => 'array|nullable',
            'category.*' => 'numeric',
            'status'     => 'array|nullable',
            'status.*'   => 'numeric',
            'privacy'    => 'array|nullable',
            'privacy.*'  => 'string',
            'type'       => 'array|nullable',
            'type.*'     => 'numeric',
            'sort_by'    => 'string|nullable|in:created_data_asc,created_data_desc',
        ];
    }

    public function formattedFilter(): array
    {
        return [
            'project_privacy'     => $this->mapConstants($this->get('privacy'), $this->privacyMap),
            'project_type_id'     => $this->get('type'),
            'project_status_id'   => $this->get('status'),
            'project_category_id' => $this->get('category'),
        ];
    }
}
