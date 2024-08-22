<?php

namespace App\Http\Requests\Discussion;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddCommentRequest extends FormRequest
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
            'comment'         => 'required|string',
            'comment_id'      =>  Rule::exists('discussions', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'attachment'      => 'array|max:5',
            'attachment.*'    => 'mimes:jpg,jpeg,webp,png,pdf,mp3,doc,docx,xlsx,xls,pptx,pptm,odp,ppt,mp4,mov,wmv,avi,webm,mkv,mpeg-2|max:1024',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'module_id.required' => __('responses.module_id_required'),
            'comment.required'   => __('responses.comments_field'),
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
