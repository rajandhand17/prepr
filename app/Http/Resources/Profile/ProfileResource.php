<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        $response['user']=[];
        $response['user_personal']=[];
        $response['user_experience']=[];
        $response['user_education']=[];
        $response['user_patent']=[];
        $response['user_skill']=[];
        $response['user_certificates']=[];
        $response['user']['id'] = $this->id;
        $response['user']['username'] = $this->username;
        $response['user']['email'] = $this->email;
        $response['user']['profile_image'] = $this->profile_image;

        if ($this->userPersonal) {
            $response['user_personal']['about'] = $this->userPersonal->about;
            $response['user_personal']['gender'] = $this->userPersonal->gender;
            $response['user_personal']['date_of_birth'] = $this->userPersonal->date_of_birth;
            $response['user_personal']['purpose'] = $this->userPersonal->purpose;
            $response['user_personal']['user_type'] = $this->userPersonal->user_type;
            $response['user_personal']['recent_immigrant'] = $this->userPersonal->recent_immigrant;
            $response['user_personal']['indigenous_group'] = $this->userPersonal->indigenous_group;
        }
        if(count($this->userExperience) > 0){
            foreach($this->userExperience as $key=>$single_user_experience){
                $response['user_experience'][$key]['company'] = $single_user_experience->company;
                $response['user_experience'][$key]['position'] = $single_user_experience->position;
                $response['user_experience'][$key]['start_date'] = $single_user_experience->start_date;
                $response['user_experience'][$key]['end_date'] = $single_user_experience->end_date;
                $response['user_experience'][$key]['address'] = $single_user_experience->address;
                $response['user_experience'][$key]['state'] = $single_user_experience->state;
                $response['user_experience'][$key]['country'] = $single_user_experience->country;
                $response['user_experience'][$key]['description'] = $single_user_experience->description;
            }
        }

        if(count($this->userEducation)>0) {

            foreach ($this->userEducation as $key=> $single_user_education){
            $response['user_education'][$key]['university'] = $single_user_education->university;
            $response['user_education'][$key]['degree'] = $single_user_education->degree;
            $response['user_education'][$key]['start_date'] = $single_user_education->start_date;
            $response['user_education'][$key]['end_date'] = $single_user_education->end_date;
            $response['user_education'][$key]['address'] = $single_user_education->address;
            $response['user_education'][$key]['description'] = $single_user_education->description;
            }
        }
        if($this->userPatents){
            foreach($this->userPatents as $key => $single_user_patient){
                $response['user_patent'][$key]['title']=$single_user_patient->title;
                $response['user_patent'][$key]['name']=$single_user_patient->name;
                $response['user_patent'][$key]['patent_date']=$single_user_patient->patent_date;
                $response['user_patent'][$key]['description']=$single_user_patient->description;
            }
        }

        if($this->userSkills){
            foreach ($this->userSkills as $key => $singluar_skill){
                    $response['user_skill'][$key]=$singluar_skill->skill;
            }
        }

        if($this->userCertificates){
            foreach ($this->userCertificates as $key => $singluar_certificate){
                $response['user_certificates'][$key]['company']=$singluar_certificate->company;
                $response['user_certificates'][$key]['name']=$singluar_certificate->name;
                $response['user_certificates'][$key]['start_date']=$singluar_certificate->start_date;
                $response['user_certificates'][$key]['end_date']=$singluar_certificate->end_date;
                $response['user_certificates'][$key]['description']=$singluar_certificate->description;
            }
        }

        return $response;
    }
}
