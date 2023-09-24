<?php

namespace App\Http\Requests\Manage\ResourceModule;

use App\Services\Manage\LabProgramService;
use App\Services\Manage\ResourceModuleService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use League\Container\Exception\NotFoundException;

class UpdateResourceModuleRequest extends FormRequest
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
        $labProgram =ResourceModuleService::getResourceModuleBasedOnSlug(request()->route('slug'));
        if (!$labProgram) {
            throw new NotFoundException();
        }
        $base_rules = [
            'title'                  => 'required|max:255|unique:lab_programs,title,'.$labProgram->id,
            'description'            => 'required',
            'organization_id'        => 'required|exists:organizations,uuid',
            'privacy'                => 'required|in:yes,no',
            'status'                 => 'required|in:draft,published,archive',
            'is_global'              => 'required|in:yes,no'

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

    public function messages(){
        return [
            'title.required'                 => __('responses.title_required'),
            'title.unique'                   => __('responses.lab_program_title_unique'),
            'description.required'           => __('responses.description_required'),
            'organization_id.required'       => __('responses.organization_id_required'),
            'organization_id.exists'         => __('responses.organization_not_exists'),
            'privacy.required'               => __('responses.privacy_required'),
            'privacy.in'                     => __('responses.choose_yes_no'),
            'status.required'                => __('responses.status_required'),
            'status.in'                      => __('responses.status_in'),
            'is_global.required'             => __('responses.choose_yes_no')
        ];
    }
}
