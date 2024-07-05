<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use Illuminate\Contracts\Validation\ValidationRule;

class LabSearchRequest extends AdvanceSearchBaseFormRequest
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
            'type'       => 'array|nullable',
            'type.*'     => 'string',
            'sort_by'    => 'string|nullable|in:created_data_asc,created_data_desc',
        ];
    }

    public function formattedFilter(): array
    {
        return [
            'status'          => $this->mapConstants($this->get('status'), $this->statusMap),
            'lab_category_id' => $this->get('category'),
            'privacy'         => $this->mapConstants($this->get('privacy'), $this->privacyMap),
            'skills_id'       => $this->get('skill'),
            'level_id'        => $this->get('level'),
            'duration_id'     => $this->get('duration'),
            'type_id'         => $this->mapConstants($this->get('type'), [
                'access'  => 0,
                'onboard' => 1,
                'engage'  => 2,
                'grow'    => 3,
            ]),
        ];
    }
}
