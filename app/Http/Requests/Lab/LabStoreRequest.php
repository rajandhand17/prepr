<?php

namespace App\Http\Requests\Lab;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
class LabStoreRequest extends FormRequest
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
        $achievement_en_switch = $this->request->get('achievement_en_switch');
        $associated_challenge_switch = $this->request->get('associated_challenge_switch');
        $associated_resource_switch = $this->request->get('associated_resource_switch');
        $data= [
            "title"=>"required|unique:labs,title",
            "description"=>"required",
            "organization_id"=>"required",
            "location"=>"required",
            "category_id"=>"required",
            "skills"=>"required",   
            "tag"=>"required",
        ];
        if($achievement_en_switch=="yes"){
            $data['achievement_name']="required";
            $data['achievement_points']="required";
            $data['achievement_condition']="required";
            $data['achievement_image']="required";
        }

        if($associated_challenge_switch=="yes"){
            $data['challenge_id']="required|array";
        }
        if($associated_resource_switch=="yes"){
            $data['challenge_path_id']="required|array";
        }
        return $data;
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
            'title.required' => __('notification.notification_title_req'),
            'title.unique' => __('responses.lab_title_unique'),
            'description.required' => __('notification.notification_tdfdfir'),
            'organizartion_id.required' => __('notification.notification_toir'),
            'location.required' => __('notification.notification_lirr'),
            'category_id.required' => __('notification.notification_cat'),
            'skills.required' => __('notification.notification_skillmbs'),
            'tag.required' => __('labels.labels_lab_tmbs'),
            'achievement_name.required' => __('responses.acheivement_name'),
            'achievement_points.required' => __('responses.acheivement_point'),
            'achievement_condition.required' => __('responses.achievement_condition'),
            'achievement_image.required' => __('responses.achievement_image'),
            'challenge_id.required' => __('responses.challenge_id'),
            'challenge_path_id.required' => __('responses.challenge_path'),

        ];
    }
}
