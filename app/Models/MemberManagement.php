<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Organization;
use App\Models\User;

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
    
  /***
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'inviter_id');
    }

    
    public function index($component,$slug,$request)
    {    
        try {
            $module_type="0";
            $module_id="";
            if($component=="organisation"){
                $module_type="0";
                $module_id=Organization::select('id')->where("slug",$slug)->first()->id;
            }
            $listing=static::with(['user'])->where(["module_id"=>$module_id,"module_type"=>$module_type]);
            if(!empty($request->org_id)){
                $listing->where('id', $request->org_id);
            }
            if(!empty($request->role)){
                $listing->where('role', $request->role);
            }
            if (!empty($request->email_status)) {
                if($request->email_status=="scheduled"){
                    $email_status="0";
                }elseif($request->email_status=="sent"){
                    $email_status="1";
                }elseif($request->email_status=="fail"){
                    $email_status="2";
                }
                $listing->where('email_status', $email_status);
            }
            if (!empty($request->searchname)) {
                $listing->where(function ($q) use ($request) {
                    $q->whereHas('user', function ($q) use ($request){
                        $q->where('username', 'like', '%' . $request->searchname . '%')->orWhere('full_name', 'like', '%' . $request->searchname . '%');
                    })->orWhere('email', 'like', '%' .$request->searchname .'%');
                });
            }
            $listing=$listing->get();
            if(!$listing->isEmpty()){
                return $listing;
            }else{
                return false;
            }
       
        } catch (\Exception $e) {
            return false;       
        }
    }
    public function deletes($component,$slug,$request)
    {     
        try{
            $member_manger=static::whereIn("id",$request->id)->delete();
            if($member_manger){
                return true;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;        
        }
    }

    public function create($component,$slug,$request)
    {   
       try{
        if ($request->invite_type == 'email') {
            $request['role'] = $request->role;
            $invitedMembers = array();
            $invalidEmailData = array();
            $alreadyExistEmailData = array();
            if (!empty($request->user_invite_email)) {
                $userInviteEmailData = explode(',', $request->user_invite_email);
                foreach($userInviteEmailData as $inviteEmail){
                    if (filter_var(trim($inviteEmail), FILTER_VALIDATE_EMAIL)){
                        if (!static::where(['organisation_id' => (int) $request->organisation_id, 'role' => $request->orgRole, 'email' => trim($inviteEmail)])->exists()){
                           
                        } else {
                            $alreadyExistEmailData[] = trim($inviteEmail);
                            $responce = ['success' => false, 'error' => true, 'alreadyExistEmailData' => $alreadyExistEmailData, 'invalidEmailData' => $invalidEmailData, 'message' => __('notification.notification_peveiel')];
                        }
                    }
                    
                }
            }
        } elseif ($request->invite_type == 'network'){
            $request['role'] =$request->role;
        }


       } catch (\Exception $e) {
        return $e;
        return false;      
       }
    }
}
