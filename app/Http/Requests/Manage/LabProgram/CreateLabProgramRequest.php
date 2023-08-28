<?php

namespace App\Http\Requests\Manage\LabProgram;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateLabProgramRequest extends FormRequest
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
            'title'                  => 'required|unique:lab_programs,title',
            'description'            => 'required',
            'lab_id'                 => 'required|exists:labs,uuid',
            'user_id'                => 'required|exists:users,id',
            'privacy'                => 'required|in:yes,no',
            'status'                 => 'required',
            'is_auto_create'         => 'required',
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
            'description.required'   => 'responses.lab_program_description_required',
            'lab_id.required'        => 'responses.lab_program_required',
            'lab_id.exists'          => 'responses.lab_program_lab_id_unique',
            'user_id.required'       => 'responses.lab_program_user_id_required',
            'user_id.exists'         => 'responses.lab_program_user_id_unique',
            'privacy.required'       => 'responses.lab_program_privacy_required',
            'privacy.in'             => 'responses.lab_program_privacy_in',
            'status.required'        => 'responses.lab_program_status_required',
            'is_auto_create.required'=> 'responses.lab_program_is_auto_create_required',
            'trophy.required'        => 'responses.lab_program_trophy_required',
        ];
    }
}
