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
            "title"=>"required",
            "description"=>"required",
            "organizartion_id"=>"required",
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
            'description.required' => __('notification.notification_tdfdfir'),
            'organizartion_id.required' => __('notification.notification_peeief'),
            'location.required' => __('notification.notification_peeief'),
            'category_id.required' => __('notification.notification_peeief'),
            'skills.required' => __('notification.notification_peeief'),
            'tag.required' => __('notification.notification_peeief'),
            'achievement_name.required' => __('notification.notification_peeief'),
            'achievement_points.required' => __('notification.notification_peeief'),
            'achievement_condition.required' => __('notification.notification_peeief'),
            'achievement_image.required' => __('notification.notification_peeief'),

        ];
    }
}
