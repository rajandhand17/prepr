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

    
    public function list($component,$slug,$request)
    {    
        try {
            $module_type="0";
            $module_id="";
            
            if($component=="organization"){
                $module_type="0";
                $module_id=Organization::select('id')->where("slug",$slug)->first();
                if($module_id){
                    $module_id=$module_id->id;
                }
            }else{
                $response= ['success' => false, 'message' => __('responses.wrong_component'),"code"=>404];
                return $response; 
            };
            if($module_id===null && $module_id==""){
                $response= ['success' => false, 'message' => __('labels.labels_org_noof'),"code"=>404];
                return $response;
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
                $response= ['success' => true,"data"=>$listing, 'message' => __('labels.labels_org_noof'),"code"=>200 ];
                return $response;
            }else{
                $response= ['success' => true, 'message' => __('notification.notification_mnf'),"code"=>200];
                return $response;
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

    public function insert($invitee,$request)
    {   
       try{
        if(!MemberManagement::where(['module_id' => (int) $request->module_id, 'role' => $request->role, 'email' => trim($invitee)])->exists()){
           $member_management_data=new MemberManagement();     
           $member_management_data->type             = $request->type;
           $member_management_data->invite_type      = $request->invite_type;
           $member_management_data->role             = $request->role;
           $member_management_data->email            = trim($invitee);
           $member_management_data->module_id        = (int) $request->module_id;
           $member_management_data->inviter_id       = (int) $request->inviter_id;
           $member_management_data->subject_line     = $request->subject_line;
           $member_management_data->email_body       = $request->email_body;
           $member_management_data->invite_status    = $request->invite_status;
           $member_management_data->email_status     = "0";
           $member_management_data->email_resend_count="0";
           if($member_management_data->save()){
             return true;
           }
           return false;
        }else{
            return "already_exists";
        }
       }catch (\Exception $e) {
        return false;      
       }
    }


    public function getRecordsFromCsv($request)
    {    
        try {
            $invite_type="3";
            $csv_email_data = array();
            /** checking extension of file  */
           if(in_array($request->file('invite_email')->getClientMimeType(), array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv'))){  
                if (($handle = fopen($request->invite_email, "r")) !== false){
                    $header = fgetcsv($handle, 0, ',');
                    $count_header = count($header);
                    /**Checking columns names in csv  */
                    if ($count_header == 2  && in_array('Name', $header) && in_array('Email', $header)){
                        /**checking place of email column one or two */
                      if($header[0]=="Email"){
                            $email_column=0;
                       }else{
                           $email_column=1;
                       }
                    }else{
                       return false;
                    }
                    /**getting data from csv and convert in array */
                    while (($csv_get_data = fgetcsv($handle, 1000, ",")) !== false){
                        $invitee[] = $csv_get_data[$email_column];
                    }
                    fclose($handle);
                    return $invitee;
                }
                return false;
           }
        } catch (\Exception $e) {
            return false;
        }
    }
}
