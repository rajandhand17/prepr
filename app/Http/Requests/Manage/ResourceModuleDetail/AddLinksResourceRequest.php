<?php

namespace App\Http\Requests\Manage\ResourceModuleDetail;

use App\Services\Manage\ResourceModuleService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use League\Container\Exception\NotFoundException;

class AddLinksResourceRequest extends FormRequest
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
            'title'                  => 'required|max:255',
            'path'                   => 'required',
            'social_link_id'         => 'required',

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
            'path.unique'                   => __('responses.lab_program_title_unique'),
            'social_link_id.unique'          => __('responses.lab_program_title_unique'),
        ];
    }
}
