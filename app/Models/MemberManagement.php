<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Organization;
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

    
    public function index($component,$slug)
    {   
        try {
            if($component=="organisation"){
                $component="0";
                $module_id=Organization::select('id')->where("slug",$slug)->first()->id;
            }else{
                $module_id=""; 
            }
          if($module_id){
            $listing=static::where(["module_id"=>$module_id,"module_type"=>$component])->get();
            if(!$listing->isEmpty()){
                return $listing;
            }else{
                return false;
            }
        }else{
            return false;
        }
        } catch (\Exception $e) {
            return false;       
        }
    }
    public function deleteMultiple($component,$slug,$request)
    {   
        try{
            $member_manger=static::whereIn("id",[$request->id])->delete();
            if($member_manger){
                return true;
            }else{
                return false;
            }
        }catch (\Exception $e){
            return false;        
        }
    }

    public function create($component,$slug,$request)
    {
       try{
        if($component=="organisation"){
            $component="0";
            $module_id=Organization::select('id')->where("slug",$slug)->first()->id;
        }else{
            $module_id=""; 
        } 
          $member_manger=new MemberManagement();
          $member_manger->type=$request->type;
          $member_manger->invite_type=$request->invite_type;
          $member_manger->module_id=$request->module_id;
          $member_manger->module_type=$request->module_type;
          $member_manger->inviter_id=$request->inviter_id;
  //        $member_manger->invitee_id=$request->invitee_id;
          $member_manger->role=$request->role;
          $member_manger->invite_status=$request->invite_status;
          $member_manger->email=$request->email;
          $member_manger->email_status=$request->email_status;
//          $member_manger->email_response=$request->email_response;
   //       $member_manger->email_resend_status=$request->email_resend_status;
          $member_manger->email_resend_count=$request->email_resend_count;
          $member_manger->subject_line=$request->subject_line;
          $member_manger->email_body=$request->email_body;
          $member_manger->save();
          return $member_manger->id;
          if($member_manger->id){
             return true; 
          }

       } catch (\Exception $e) {
        return $e;
        return false;      
       }
    }
}
