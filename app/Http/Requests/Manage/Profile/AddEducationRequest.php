<?php

namespace App\Http\Requests\Manage\Profile;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AddEducationRequest extends FormRequest
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
            'user_id'        => 'required',
            'university'     => 'required',
            'degree'         => 'required',
            'start_date'     => 'required|date|before_or_equal:'.Carbon::now()->toDateTimeString(),
            'end_date'       => 'required|date|before_or_equal:'.Carbon::now()->toDateTimeString(),
        ];
    }
}
