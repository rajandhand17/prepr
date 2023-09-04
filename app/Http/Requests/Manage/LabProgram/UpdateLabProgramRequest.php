<?php

namespace App\Http\Requests\Manage\LabProgram;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLabProgramRequest extends FormRequest
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
            'title'                  => 'unique:lab_programs,title',
            'description'            => 'required',
            //'lab_id'                 => 'required|exists:labs,uuid',
            'privacy'                => 'required|in:yes,no',
            'status'                 => 'required',
            'is_auto_created'         => 'required',
            'trophy'                 => 'required',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }

    public function messages()
    {
        return [
            'title.required'         => __('responses.lab_program_title_required'),
            'title.unique'           => __('responses.lab_program_title_unique'),
            'description.required'   => __('responses.lab_program_description_required'),
            'lab_id.required'        => __('responses.lab_program_required'),
            'lab_id.exists'          => __('responses.lab_program_lab_id_unique'),
            'privacy.required'       => __('responses.lab_program_privacy_required'),
            'privacy.in'             => __('responses.lab_program_privacy_in'),
            'status.required'        => __('responses.lab_program_status_required'),
            'is_auto_created.required'=>__('responses.lab_program_is_auto_create_required'),
            'trophy.required'         =>__('responses.lab_program_trophy_required'),
        ];
    }
}
