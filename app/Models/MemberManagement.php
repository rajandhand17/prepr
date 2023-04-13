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

    protected $table = 'member_management';

    protected $fillable = [
        'type',
        'invite_type',
        'module_id',
        'module_type',
        'inviter_id',
        'invitee_id',
        'role',
        'invite_status',
        'email',
        'email_status',
        'email_response',
        'email_resend_status',
        'email_resend_count',
        'subject_line',
        'email_body',
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
            if($module_id===null && $module_id==""){
                return false;
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
            if (!empty($request->searchname)){
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
        if ($request->invite_type == 'email'){
            $invite_type="0";
            $request['role'] = $request->role;
            $invited_members = array();
            $invalid_email_data = array();
            $already_exist_email_data = array();
            if (!empty($request->user_invite_email)){
                $user_invite_email_data = explode(',', $request->user_invite_email);
           foreach ($user_invite_email_data as $invite_email){
            if (filter_var(trim($invite_email), FILTER_VALIDATE_EMAIL)){
                if (!static::where(['module_id' => (int) $request->organisation_id, 'role' => $request->role, 'email' => trim($invite_email)])->exists()){
                        if($request->type=="invite"){
                            $type="0";
                        }elseif($request->type=="join_request"){
                            $type="1";
                        }
                        $invite_data['type']             = $request->type;
                        $invite_data['invite_type']      = $invite_type;
                        $invite_data['role']             = $request->role;
                        $invite_data['email']            = trim($invite_email);
                        $invite_data['module_id']        = (int) $request->organisation_id;
                        $invite_data['inviter_id']       = (int) $request->inviter_id;
                        $invite_data['subject_line']     = $request->subject_line;
                        $invite_data['email_body']       = $request->email_message;
                        $invite_data['invite_status']    = $request->auto_invite_status;
                        $invite_data['invite_status']    = "0";
                        $invite_data['email_status']     = "0";
                        $invite_data['email_resend_count']="0";
                        $invited_members[]= $invite_data;
                 }else{
                    $already_exist_email_data[] = trim($invite_email);
                }
            }else{
                $invalid_email_data[] = trim($invite_email);
            }

            }
            if(!empty($invited_members)){
                if (static::insert($invited_members)) {
                    $responce = ['success' => true, 'error' => false, 'already_exist_email_data' => $already_exist_email_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_edas')];
                }
            }else{
                $responce = ['success' => false, 'error' => true, 'already_exist_email_data' => $already_exist_email_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_ntcedad')];
            }
            return $responce;
        }
       }elseif ($request->invite_type == 'network'){
            $invited_members = array();
            $already_exist_network_data = array();
            if(!empty($request->inviteUsers)){
                foreach ($request->inviteUsers as $invite_member){
                    $user_data = User::select('email')->where(['id' => (int) $invite_member])->first();
                    if (!empty($user_data)){
                        if(!static::where(['module_id' => (int) $request->organisation_id, 'role' => $request->role, 'email' => trim($user_data['email'])])->exists()){
                            $invite_data['type']             = $request->type;
                            $invite_data['invite_type']      = $request->invite_type;
                            $invite_data['role']             = $request->role;
                            $invite_data['module_id']        = (int) $request->organisation_id;
                            $invite_data['inviter_id']       = (int) $request->inviter_id;
                            $invite_data['invitee_id']       = $invite_member;
                            $invite_data['subject_line']     = $request->subject_line;
                            $invite_data['email']            = trim($user_data['email']);
                            $invite_data['email_message']    = $request->email_message;
                            $invite_data['invitation_status']= $request->auto_invite_status;
                            $invite_data['invite_status']    = '0';
                            $invite_data['email_status']     = '0';
                            $invited_members[]                = $invite_data;
                        }else{
                            $already_exist_network_data[]      = trim($user_data['email']);
                            $responce = ['success' => false, 'error' => true, 'alreadyExistNetworkData' => $already_exist_network_data, 'invalidNetworkData' => [], 'message' => __('notification.notification_ara') . $request->role . __('labels.labels_mm_role')];
                        }
                    }
                }
            }else{
                $responce = ['success' => false, 'error' => true, 'already_exist_email_data' => $already_exist_network_data, 'invalid_email_data' =>[], 'message' => __('notification.notification_ntcedad')];
            }
        
       }
    } catch (\Exception $e) {
        return false;      
       }
}
}
