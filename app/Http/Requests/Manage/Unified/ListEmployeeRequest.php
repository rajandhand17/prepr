<?php

namespace App\Http\Requests\Manage\Unified;

use App\Helpers\CryptHelper;
use App\Http\Requests\BaseRequest;
use App\Rules\UnifiedStateRule;

class ListEmployeeRequest extends BaseRequest
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
        return [
            'connection_id' => 'required',
            'state'         => ['required', new UnifiedStateRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'connection_id.required' => __('responses.unified_connection_id_required'),
            'state.required'         => __('responses.unified_state_required'),
        ];
    }

    public function formatted(): array
    {
        return [
            ...$this->all(),
            'state'        => CryptHelper::decrypt($this->get('state')),
        ];
    }
}
