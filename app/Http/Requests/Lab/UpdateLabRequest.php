<?php

namespace App\Http\Requests\Lab;

use App\Models\Lab;
use App\Services\LabService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLabRequest extends FormRequest
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
        $lab=LabService::checkSlug(request()->route('slug'));
        $achievement_en_switch = $this->request->get('is_achievement_enabled');
        if($lab){
            $base_rules=[
            'cover_image' => 'image|mimes:jpeg,jpg,png,webp|max:1024',
            'title'       => 'required|max:255|unique:labs,title,'.$lab->id,
            ];
        }else{
            $base_rules=[
                'cover_image' => 'image|mimes:jpeg,jpg,png,webp|max:1024',
                'title'       => 'required|max:255|unique:labs,title,',
            ];
        }
        $base_rules = [
            'request_type'=> 'required|in:draft,publish,archive',
            'description' => 'required_if:request_type,publish|nullable',
            'organization_id'=> 'required|exists:organizations,id',
            'category_id'    => 'required|exists:categories,id',
            'privacy'=> 'required_if:request_type,publish|in:yes,no',
            'location' => 'required_if:request_type,publish|nullable',
            'latitude' => 'required_if:request_type,publish|nullable',
            'longitude'=> 'required_if:request_type,publish|nullable',
            'country'  => 'required_if:request_type,publish|nullable',
            'city'     => 'required_if:request_type,publish|nullable',
            'skills'        => 'required_if:request_type,publish|nullable|array',
            'skills.*'      => 'numeric',
            'skill_groups'  => 'nullable|array',
            'skill_groups.*'=> 'numeric',
            'skill_stacks'  => 'nullable|array',
            'skill_stacks.*'=> 'numeric',
            'tags'        => 'required_if:request_type,publish|nullable|array',
            'tags.*'      => 'numeric',
            'tag_groups'  => 'nullable|array',
            'tag_groups.*'=> 'numeric',

            'is_notification_enabled'=> 'in:yes,no',

            'is_achievement_enabled'=> 'in:yes,no',

            'is_sequential'=> 'in:yes,no',

            'is_resource_sequential'=> 'in:yes,no',
        ];

        if ($this->request->has('external_links')) {
            $base_rules['external_links'] = 'array';
            $base_rules['external_link_ids'] = 'array|exists:social_links,id';
            $base_rules['external_links.*'] = 'url';
            $base_rules['external_link_ids.*'] = 'numeric';
        }

        if ($achievement_en_switch == 'yes') {
            $base_rules['achievement_name'] = 'required';
            $base_rules['achievement_points'] = 'required';
            $base_rules['achievement_conditions'] = 'required|array';
            $base_rules['achievement_image'] = 'required';
        }

        if ($this->request->has('lab_programs')) {
            $base_rules['lab_programs'] = 'array';
            $base_rules['lab_programs.*'] = 'numeric';
        }

        if ($this->request->has('challenges')) {
            $base_rules['challenges'] = 'array';
            $base_rules['challenges.*'] = 'numeric';
        }

        if ($this->request->has('challenge_paths')) {
            $base_rules['challenge_paths'] = 'array';
            $base_rules['challenge_paths.*'] = 'numeric';
        }

        if ($this->request->has('resource_modules')) {
            $base_rules['resource_modules'] = 'array';
            $base_rules['resource_modules.*'] = 'numeric';
        }

        if ($this->request->has('resource_groups')) {
            $base_rules['resource_groups'] = 'array';
            $base_rules['resource_groups.*'] = 'numeric';
        }

        if ($this->request->has('resource_collections')) {
            $base_rules['resource_collections'] = 'array';
            $base_rules['resource_collections.*'] = 'numeric';
        }

        if ($this->request->has('invite_type')) {
            $check_invite_type = $this->request->get('invite_type');
            $base_rules['subject_line'] = 'max:250';
            $base_rules['email_body'] = 'max:2000';
            $base_rules['auto_invite'] = 'required|in:yes,no,na';

            if ($check_invite_type == 'csv') {
                $base_rules['invite_email'] = 'required|mimes:csv,txt';
            }
            if ($check_invite_type == 'email') {
                $base_rules['invite_email'] = 'required|array';
                $base_rules['invite_email.*'] = 'required|email';
            }
        }

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
        return [
            'title.required'                 => __('notification.notification_title_req'),
            'title.unique'                   => __('responses.lab_title_unique'),
            'description.required'           => __('notification.notification_tdfdfir'),
            'organizartion_id.required'      => __('notification.notification_toir'),
            'location.required'              => __('notification.notification_lirr'),
            'category_id.required'           => __('notification.notification_cat'),
            'skills.required'                => __('notification.notification_skillmbs'),
            'tag.required'                   => __('labels.labels_lab_tmbs'),
            'achievement_name.required'      => __('responses.acheivement_name'),
            'achievement_points.required'    => __('responses.acheivement_point'),
            'achievement_condition.required' => __('responses.achievement_condition'),
            'achievement_image.required'     => __('responses.achievement_image'),
            'challenge_id.required'          => __('responses.challenge_id'),
            'challenge_path_id.required'     => __('responses.challenge_path'),

        ];
    }
}
