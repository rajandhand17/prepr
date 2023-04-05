<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberManagement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'member_managements';

    protected $fillable = [
        'invite_type',
        'module_id',
        'module_type',
        'inviter_id',
        'invitee_id',
        'role',
        'invite_status',
        'email',
        'email_status',
        'email_responce',
        'email_resend_status',
        'subject_line',
        'email_message',
        'fail_schedule',
        'is_exist',
        'is_evaluator',
        'is_join_request',
        'join_request_status',
        'lab_users_id',
        'privacy',
        'auto_invite_status',
        'assign_role',
        'is_auto_created',
        'user_status',
    ];

    
    public function view($language)
    {
        try {
             $listing=static::get();
             if(!$listing->isEmpty()){
             $listing->transform(function ($value) {
               
                /**change the value of invite type */
                if($value['invite_type']==0){ $value['invite_type']="email"; }
                if($value['invite_type']==1){ $value['invite_type']="network"; }
                if($value['invite_type']==2){ $value['invite_type']="other"; }
                /**change the value of module type */
                if($value['module_type']==0){ $value['module_type']="lab"; }
                if($value['module_type']==1){ $value['module_type']="challenge"; }
                if($value['module_type']==2){ $value['module_type']="project"; }
                if($value['module_type']==3){ $value['module_type']="other"; }
                /**change the value for invite status */
                if($value['invite_status']==0){ $value['invite_status']="invited"; }
                if($value['invite_status']==1){ $value['invite_status']="accepted"; }
                if($value['invite_status']==2){ $value['invite_status']="pending"; }
                if($value['invite_status']==3){ $value['invite_status']="declined"; }
                /**change the value for email status */
                if($value['email_status']==0){ $value['email_status']="sent"; }
                if($value['email_status']==1){ $value['email_status']="fail"; }
                if($value['email_status']==2){ $value['email_status']="schedule"; }
                if($value['email_status']==3){ $value['email_status']="other"; }
                /**change the value for user status */
                if($value['user_status']==0){ $value['user_status']="delete"; }
                if($value['user_status']==1){ $value['user_status']="active"; }
                
                return $value;
            });
            return $listing;
        }else{
            return false;
        }
        } catch (\Exception $e) {
            return $e;
        }
    }  

    public function deletes($slug,$language)
    {
        try {
            $exists=static::select("id")->where("id",$slug)->first();
            if($exists!==null){
                $organization=static::where("id",$slug)->delete();
                if($organization){
                     return true;        
                }else{
                    return false;
                }
            }else{
                return "not_exists";
            }
          
        } catch (\Exception $e){
            return false;        
        }
    }

    public function deleteMultiple($slug)
    {    
        try {  
                $organization=static::whereIn("id",explode(",",$slug))->delete();
                if($organization){
                    return true;
                }else{
                    return false;
                }
        } catch (\Exception $e){
            return $e;
            return false;        
        }
    }
}
