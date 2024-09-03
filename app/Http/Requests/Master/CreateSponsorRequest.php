<?php

namespace App\Http\Requests\Master;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSponsorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $base_rules = [
            'title'     => 'required|unique:hosts,title',
            'link'      => 'required|url|unique:hosts,link',
            'image'     => 'required|mimes:jpeg,jpg,png,webp|max:1024',
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

    public function messages()
    {
        return[
            'title.required' => __('responses.sponsor_host_title_required'),
            'title.unique'   => __('responses.sponsor_host_title_unique'),
            'link.required'  => __('responses.sponsor_host_link_required'),
            'link.unique'    => __('responses.sponsor_host_link_unique'),
            'link.url'       => __('responses.website_valid_url_pattern'),
        ];
    }
}
