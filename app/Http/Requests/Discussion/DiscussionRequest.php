<?php

namespace App\Http\Requests\Discussion;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DiscussionRequest extends FormRequest
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
        $activity = $this->route('action');
        $base_rules = [];
        if ($activity == 'add') {
            $base_rules = [
                'comment'        => 'required|string',
                'comment_id'     => 'exists:discussions,id',
            ];
        } elseif ($activity == 'like') {
            $base_rules = [
                'comment_id'=> 'required|exists:discussions,id',
            ];
        } elseif ($activity == 'dislikes') {
            $base_rules = [
                'comment_id'=> 'required|exists:discussions,id',
            ];
        }

        return $base_rules;
    }

    public function messages()
    {
        return [
            'module_id.required' => __('responses.module_id_required'),
            'comment.required'   => __('responses.reference_id_required'),
            'user_id.exists'     => __('responses.reference_id_exists'),
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
}
