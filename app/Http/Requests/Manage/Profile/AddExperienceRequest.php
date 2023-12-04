<?php

namespace App\Http\Requests\Manage\Profile;

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
            'user_id'   =>'required',
            'company'   =>'required',
            'position'  =>'required',
            'start_date'=>'required|date|before_or_equal:'.Carbon::now()->toDateTimeString(),
            'end_date'  =>'required|date|before_or_equal:'.Carbon::now()->toDateTimeString(),
            'address'   =>'required',
            'state'     =>'required',
            'country'   =>'required',
            'description'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' =>__("response.user_id_required"),
            'company.required' =>__("response.company_required"),
            'position.required' =>__("response.position_required"),
            'start_date.required' =>__("response.start_date_required"),
            'end_date.required' =>__("response.end_date_required"),
            'address.required' =>__("response.address_required"),
            'state.required' =>__("response.state_required"),
            'country.required' =>__("response.country_required"),
            'description.required' =>__("response.description_required"),
        ];
    }
}
