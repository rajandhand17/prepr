<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Organization;
use App\Models\User;
use DB;

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
            if (!empty($request->email_status)){    
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
                        $q->where('username', 'like', '%' . $request->searchname . '%')->orWhere('first_name', 'like', '%' . $request->searchname . '%');
                    })->orWhere('email', 'like', '%' .$request->searchname .'%');
                });
            }
            $listing=$listing->get();
            if(!$listing->isEmpty()){
                return $listing;
            }else{
                return false;
            }
       
        }catch (\Exception $e) {
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
        $records=array();
        $already_exists_data=array();
        $invalid_email_data=array();
        $user_data=array();
        $request->type="0";
        $request->invite_status="0";
        if(!in_array($request->invite_type,["csv","email","network"])){
            $response = ['success' => false, 'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('responses.member_manage_type')];
            return $response;
        }
        if($request->invite_type == 'csv'){
            $invite_type="3";
            $csv_email_data = array();
            $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
            
            if (in_array($request->file('invite_email')->getClientMimeType(), $mimes)){
                if (!empty($request->file('invite_email'))){
                    $invitee = array();
                    if (($handle = fopen($request->invite_email, "r")) !== false) {
                        $header = fgetcsv($handle, 0, ',');
                        $count_header = count($header);
                        if ($count_header == 2  && in_array('Name', $header) && in_array('Email', $header)) {
                           if($header[0]=="Email"){
                               $a=0;
                           }else{
                                $a=1;
                           }
                        }else{
                            $response = ['success' => false,'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_ycsvfdfrf')];
                        }
                        while (($csv_get_data = fgetcsv($handle, 1000, ",")) !== false) {
                            $invitee[] = $csv_get_data[$a];
                        }
                        fclose($handle);
                  }
                }
            }else{
                return  $response = ['success' => false,'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('labels.labels_lab_tiufmbaf')];
            }
            
        }else{
           $invitee = explode(',', $request->invite_email);
        }
       
        if($request->invite_type=="email"){
            $invite_type="0";
        }
        if($request->invite_type == 'network'){
            $invite_type="1";
            foreach ($invitee as $invite_member){
                $user_data[] = User::select('email')->where(['id' => (int) $invite_member])->first()->email;
            }      
            $invitee=$user_data;
        }
        if(!$invitee){
            $response = ['success' => false,'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' =>__('notification.notification_ntcedad')];
        }
        $invite_email=$invitee;
            foreach ($invite_email as $key => $invite_email){
                if (filter_var(trim($invite_email), FILTER_VALIDATE_EMAIL)){
                    if(!static::where(['module_id' => (int) $request->module_id, 'role' => $request->role, 'email' => trim($invite_email)])->exists()){
                        $user_data = User::select('id')->where(['email'=>$invite_email])->first();
                        if(isset($user_data->id)){
                           $member_management_data['invitee_id']    = $user_data->id;
                        }
                        $member_management_data['type']             = $request->type;
                        $member_management_data['invite_type']      = $invite_type;
                        $member_management_data['role']             = $request->role;
                        $member_management_data['email']            = trim($invite_email);
                        $member_management_data['module_id']        = (int) $request->module_id;
                        $member_management_data['inviter_id']       = (int) $request->inviter_id;
                        $member_management_data['subject_line']     = $request->subject_line;
                        $member_management_data['email_body']       = $request->email_body;
                        $member_management_data['invite_status']    = $request->invite_status;
                        $member_management_data['email_status']     = "0";
                        $member_management_data['email_resend_count']="0";
                        $invited_members[]= $member_management_data;
                    }else{
                       $already_exists_data[]=trim($invite_email);
                    }
                }else{
                    $invalid_email_data[] = trim($invite_email);
                }
            }
            if(!empty($invited_members)){
               DB::beginTransaction();
                if(static::insert($invited_members)){
                    DB::commit();
                    $response = ['success' => true,'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_edas')];
                }else{
                    DB::rollback();
                    $response = ['success' => false, 'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_ntcedad')];
                }
            }else{
                $response = ['success' => false, 'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_ntcedad')];
            }
            return  $response;
    } catch (\Exception $e) {
        DB::rollback();
        return false;      
       }
    }
}
