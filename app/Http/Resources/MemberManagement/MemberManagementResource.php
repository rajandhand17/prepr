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
        $member_management = [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'invite_status' => $this->invite_status,
            'email' => $this->email,
            'email_status' => $this->email_status,
            'email_resend_status' => $this->email_resend_status,
            'invite_status' => $this->invite_status,
            'user_status' => $this->user_status,
            "user_name"=>$this->user->username,
            "user_profile_image"=>$this->user->profile_image,
        ];
      
        if($this->type==0){
            $member_management['type']="invite";
        }
        if($this->type==1){
            $member_management['type']="join-request";
        }
        if($this->type==2){
            $member_management['type']="auto-created";
        }
          //Invite type changing
          $inviteType=config("member-manager-invite-type");
          $member_management['invite_type']=$inviteType[$this->invite_type];
        //organisation, lab, challenge, project
        $moduleType=config('member-management-module-type');
        $member_management['module_type']=$moduleType[$this->module_type];
        $inviteStatus=config('member-management-invite-status');
        $member_management['invite_status']=$inviteStatus[$this->invite_status];
        return $member_management;
        
    }
}
