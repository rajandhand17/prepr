<?php

namespace App\Http\Requests\Manage\ResourceGroup;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateResourceGroupRequest extends FormRequest
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
            'title'                    => 'required|unique:resource_groups,title',
            'organization_id'          => 'required|exists:organizations,uuid',
            'description'              => 'required',
            'cover_image'              => 'nullable|mimes:jpeg,jpg,png,webp|max:1024',
            'privacy'                  => 'required|in:yes,no',
            'status'                   => 'required|in:draft,published,archive',
            'resource_ids'             => 'required|array',
            'resource_ids.*'           => 'exists:resource_modules,uuid',
            'resource_collection_ids'  => 'required|array',
            'resource_collection_ids.*'=> 'exists:resource_collections,uuid',
            'skills'                   => 'required|array',
            'skills.*'                 => 'numeric|exists:skills,id',
            'tags'                     => 'required|array',
            'tags.*'                   => 'numeric|exists:tags,id',
            'tag_groups'               => 'array',
            'level'                    => 'required|exists:levels,id',
            'duration'                 => 'required|exists:durations,id',
            'tag_groups.*'             => 'numeric|exists:tag_groups,id',
            'skill_groups'             => 'array',
            'skill_groups.*'           => 'numeric|exists:skill_groups,id',
            'skill_stacks'             => 'array',
            'skill_stacks.*'           => 'numeric|exists:skill_stacks,id',
        ];

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
}
