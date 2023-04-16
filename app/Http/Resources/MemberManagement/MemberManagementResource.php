<?php
namespace App\Http\Resources\MemberManagement;
use Illuminate\Http\Resources\Json\JsonResource;

use function PHPSTORM_META\type;

class MemberManagementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {   
        $arrayData = [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'invite_status' => $this->invite_status,
            'email' => $this->email,
            'email_status' => $this->email_status,
            'email_resend_status' => $this->email_resend_status,
            'is_exist' => $this->is_exist,
            'is_evaluator' => $this->is_evaluator,
            'is_join_request' => $this->is_join_request,
            'join_request_status' => $this->join_request_status,
            'auto_invite_status' => $this->auto_invite_status,
            'user_status' => $this->user_status,
            "user_name"=>$this->user->username,
            "user_profile_image"=>$this->user->profile_image,
        ];
   
        if($this->type==0){
            $arrayData['type']="Invite";
        }
        if($this->type==1){
            $arrayData['type']="Join_request";
        }
        if($this->type==2){
            $arrayData['type']="auto_Created";
        }
          //Invite type changing
        if($this->invite_type==0){
            $arrayData['invite_type']="email";
        }
        if($this->invite_type==1){
            $arrayData['invite_type']="network";
        }
        if($this->invite_type==2){
            $arrayData['invite_type']="join_request";
        }
        if($this->invite_type==3){
            $arrayData['invite_type']="auto_created";
        }
        //organisation, lab, challenge, project
        if($this->module_type==0){
            $arrayData['module_type']="organisation";
        }
        if($this->module_type==1){
            $arrayData['module_type']="lab";
        }
        if($this->module_type==2){
            $arrayData['module_type']="challenge";
        }
        if($this->module_type==2){
            $arrayData['module_type']="project";
        }
        if($this->invite_status==0){
            $arrayData['invite_status']="invited";
        }
        if($this->invite_status==1){
            $arrayData['invite_status']="accepted";
        }
        if($this->invite_status==2){
            $arrayData['invite_status']="pending";
        }
        if($this->invite_status==3){
            $arrayData['invite_status']="declined";
        }
        if($this->invite_status==4){
            $arrayData['invite_status']="auto_created";
        }


        return $arrayData;
        
    }
}
