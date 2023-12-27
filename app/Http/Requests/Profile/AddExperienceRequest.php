<?php

namespace App\Http\Requests\Profile;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AddExperienceRequest extends FormRequest
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
            'company'      => 'required|array',
            'company.*'    => 'max:255',
            'description'  => 'required|array',
            'description.*'=> 'required',
            'start_date'   => 'required|array',
            'start_date.*' => 'required|before:tomorrow',
            'end_date'     => 'required|array',
            'end_date.*'   => 'required|after_or_equal:start_date.*',
            'position'   => 'required|array',
            'position.*'   => 'required',
            'address'     => 'required|array',
            'address.*'    => 'required',
            'state'      => 'required|array',
            'state.*'      => 'required',
            'country'    => 'required|array',
            'country.*'    => 'required',
        ];
    }

    public function messages()
    {
        return [
            'company.*.required'     => __('response.company_required'),
            'company.*.max'          => __('response.max_content_255'),
            'description.*.required' => __('response.description_required'),
            'position.*.required'    => __('response.position_required'),
            'start_date.*.required'  => __('response.start_date_required'),
            'start_date.*.before'    => __('response.before_or_equal'),
            'end_date.*.required'    => __('response.end_date_required'),
            'end_date.*.after_or_equal'=> __('response.end_date_required'),
            'address.*.required'     => __('response.address_required'),
            'state.*.required'       => __('response.state_required'),
            'country.*.required'     => __('response.country_required'),
        ];
    }
}
