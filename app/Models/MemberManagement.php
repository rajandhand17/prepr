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
                        if($request->auto_invite_status=="invited"){
                            $auto_invite_status="0";
                        }elseif($request->auto_invite_status=="accepted"){
                            $auto_invite_status="1";
                        }elseif($request->auto_invite_status=="pending"){
                            $auto_invite_status="2";
                        }elseif($request->auto_invite_status=="declined"){
                            $auto_invite_status="3";
                        }elseif($request->auto_invite_status=="auto_created"){
                            $auto_invite_status="4";
                        }
                        $user_data = User::select('id')->where(['email'=>$invite_email])->first();
                        dd($user_data);
                    if(isset($user_data->id)){
                        $invite_data['invitee_id']             = $user_data->id;
                    }
                        $invite_data['type']             = $type;
                        $invite_data['invite_type']      = $invite_type;
                        $invite_data['role']             = $request->role;
                        $invite_data['email']            = trim($invite_email);
                        $invite_data['module_id']        = (int) $request->organisation_id;
                        $invite_data['inviter_id']       = (int) $request->inviter_id;
                        $invite_data['subject_line']     = $request->subject_line;
                        $invite_data['email_body']       = $request->email_message;
                        $invite_data['invite_status']    = $auto_invite_status;
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
                    $response = ['success' => true, 'error' => false, 'already_exist_email_data' => $already_exist_email_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_edas')];
                }
            }else{
                $response = ['success' => false, 'error' => true, 'already_exist_email_data' => $already_exist_email_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_ntcedad')];
            }
            return $response;
        }
       }elseif($request->invite_type == 'network'){
            $invite_type="1";
             $invited_members = array();
            $already_exist_network_data = array();
            if(!empty($request->user_invite_email)){ 
                $user_invite_email_data = explode(',', $request->user_invite_email);
              
                foreach ($user_invite_email_data as $invite_member){
                    $user_data = User::select('email')->where(['id' => (int) $invite_member])->first();
                    if(($user_data->email)){
                        $invite_data['type']             = $request->type;
                        if(!static::where(['module_id' => (int)$request->organisation_id, 'role' => $request->role, 'email' => trim($user_data->email)])->exists()){
                            if($request->type=="invite"){
                                $type="0";
                            }elseif($request->type=="join_request"){
                                $type="1";
                            }
                            if($request->auto_invite_status=="invited"){
                                $auto_invite_status="0";
                            }elseif($request->auto_invite_status=="accepted"){
                                $auto_invite_status="1";
                            }elseif($request->auto_invite_status=="pending"){
                                $auto_invite_status="2";
                            }elseif($request->auto_invite_status=="declined"){
                                $auto_invite_status="3";
                            }elseif($request->auto_invite_status=="auto_created"){
                                $auto_invite_status="4";
                            }
                             $invite_data['type']             = $type;
                            $invite_data['invite_type']      = $invite_type;
                            $invite_data['role']             = $request->role;
                            $invite_data['module_id']        = (int) $request->organisation_id;
                            $invite_data['inviter_id']       = (int) $request->inviter_id;
                            $invite_data['invitee_id']       = (int)$invite_member;
                            $invite_data['subject_line']     = $request->subject_line;
                            $invite_data['email']            = trim($user_data->email);
                            $invite_data['email_body']       = $request->email_message;
                            $invite_data['invite_status']    = $auto_invite_status;
                            $invite_data['email_status']     = "0";
                            $invite_data['email_resend_count']="0";
                            $invited_members[]                = $invite_data;
                           
                        }else{
                            $already_exist_network_data[]      = trim($user_data->email);
                        
                        }
                    }
                }
                if(!empty($invited_members)){
                    if (static::insert($invited_members)) {
                        $response = ['success' => true, 'error' => false, 'already_exist_email_data' => $already_exist_network_data, 'invalid_email_data' =>[], 'message' => __('notification.notification_edas')];
                    }
                }else{
                    $response = ['success' => false, 'error' => true, 'already_exist_email_data' => $already_exist_network_data, 'invalid_email_data' =>[], 'message' => __('notification.notification_ntcedad')];
                }
            }else{
                $response = ['success' => false, 'error' => true, 'already_exist_email_data' => $already_exist_network_data, 'invalid_email_data' =>[], 'message' => __('notification.notification_ntcedad')];
            }
            return $response;
       }elseif($request->invite_type =='csv'){
        $invite_type="0";
        $invited_members = array();
        $invalid_email_invited_data = array();
        $already_invited_email_data = array();
        $csv_email_data = array();
        $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
    
        if (in_array($request->file('invitee_id')->getClientMimeType(), $mimes)){
            if (!empty($request->file('invitee_id'))) {
                $csv_result_data = array();
                if (($handle = fopen($request->invitee_id, "r")) !== false) {
                    $header = fgetcsv($handle, 0, ',');
                    $count_header = count($header);
                   while (($csv_get_data = fgetcsv($handle, 1000, ",")) !== false) {
                        $csv_result_data[] = $csv_get_data;
                    }
                    fclose($handle);
                    if ($count_header == 2  && in_array('Name', $header) && in_array('Email', $header)) {
                        if(!empty($csv_result_data)) {
                            foreach ($csv_result_data as $key => $csv_data) {
                                if (filter_var(trim($csv_data[1]), FILTER_VALIDATE_EMAIL)) {
                                    // check if duplicate email in csv
                                    if (!in_array(trim($csv_data[1]), $csv_email_data)) {
                                        // check if duplicate email in already invited data
                                        if (!static::where(['module_id' => (int) $request->organisation_id, 'role' => $request->role, 'email' => trim($csv_data[1])])->exists()) {
                                            if($request->auto_invite_status=="invited"){
                                                $auto_invite_status="0";
                                            }elseif($request->auto_invite_status=="accepted"){
                                                $auto_invite_status="1";
                                            }elseif($request->auto_invite_status=="pending"){
                                                $auto_invite_status="2";
                                            }elseif($request->auto_invite_status=="declined"){
                                                $auto_invite_status="3";
                                            }elseif($request->auto_invite_status=="auto_created"){
                                                $auto_invite_status="4";
                                            }
                                            $invite_data['invite_type']      = $invite_type;
                                            $invite_data['role']             = $request->role;
                                            $invite_data['email']            = trim($csv_data[1]);
                                            $invite_data['module_id']        = (int) $request->organisation_id;
                                            $invite_data['inviter_id']       = (int) $request->inviter_id;
                                            $invite_data['subject_line']     = $request->subject_line;
                                            $invite_data['email_body']       = $request->email_message;
                                            $invite_data['invite_status']    = $auto_invite_status;
                                            $invite_data['email_resend_count']    = '0';
                                            $invite_data['email_status']     = '0';
                                            $invited_org_members[]            = $invite_data;
                                            $invited_members[] = $invite_data;
                                            $csv_email_data[] = trim($csv_data[1]);
                                           
                                        } else {
                                            $already_invited_email_data[] = trim($csv_data[1]);
                                            $response = ['success' => false, 'error' => true, 'already_invited_email_data' => $already_invited_email_data, 'invalid_email_invited_data' => $invalid_email_invited_data, 'message' => __('notification.notification_teiycsvnv')];
                                        }
                                    } else {
                                        $already_invited_email_data[] = trim($csv_data[1]);
                                        $response = ['success' => false, 'error' => true, 'already_invited_email_data' => $already_invited_email_data, 'invalid_email_invited_data' => $invalid_email_invited_data, 'message' => __('notification.notification_teiycsvnv')];
                                    }
                                }else{
                                    $invalid_email_invited_data[] = trim($csv_data[1]);
                                    $response = ['success' => false, 'error' => true, 'already_invited_email_data' => $already_invited_email_data, 'invalid_email_invited_data' => $invalid_email_invited_data, 'message' => __('notification.notification_teiycsvnv')];
                                }
                            }
                        }
                    }
                }
                if (!empty($invited_members)) {
                    if (static::insert($invited_members)) {
                        $response = ['success' => true, 'error' => false, 'invalidEmailinvitedData' => $invalid_email_invited_data, 'alreadyInvitedEmailData' => $already_invited_email_data, 'message' => __('notification.notification_csvdds')];
                    }
                } else {
                    $response = ['success' => false, 'error' => true, 'invalidEmailinvitedData' => $invalid_email_invited_data, 'alreadyInvitedEmailData' => $already_invited_email_data, 'message' => __('notification.notification_ntcsvd')];
                }
                return $response;
           }
        }
       }
    } catch (\Exception $e) {
        return $e;
        return false;      
       }
}
}
