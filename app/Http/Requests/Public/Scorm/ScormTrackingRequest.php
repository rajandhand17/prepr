<?php

namespace App\Http\Requests\Public\Scorm;

use App\Http\Requests\BaseRequest;
use App\Services\Manage\Scorm\Enum\ScormVersions;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ScormTrackingRequest extends BaseRequest
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
            'cmi'      => ['required', 'array'],
            'sco_uuid' => ['required', 'string', Rule::exists('scorm_sco', 'uuid')],
            'version'  => ['required', Rule::in(collect(ScormVersions::cases())->pluck('value')->toArray())],
        ];
    }
}
