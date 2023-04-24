<?php
namespace App\Repositories\Api\MemberManagement;
use App\Repositories\Api\MemberManagement\MemberManagementInterface;
use AWS\CRT\HTTP\Request;
use App\Models\MemberManagement;
use App\Models\User;
class MemberManagementRepository implements MemberManagementInterface{
    
    private $member_mangement;
   function __construct(MemberManagement $member_mangement)
   {
      $this->member_mangement=$member_mangement;
   }
    public function index($component,$slug,$request)
    {  
        try{
            return $this->member_mangement->list($component,$slug,$request);
         }
         catch (\Exception $e){
            return false;
         }   
    }

    public function delete($component,$slug,$request)
    {
        try {
            return $this->member_mangement->deletes($component,$slug,$request);
        } catch (\Exception $e) {
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
            $invitee = array();
            if(!in_array($request->invite_type,["csv","email","network"])){
                return ['success' => false, 'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('responses.member_manage_type')];
            }
            if($request->invite_type == 'csv'){
                $invite_type="3";
                $csv_email_data = array();
                if(in_array($request->file('invite_email')->getClientMimeType(), array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv'))){  
                    if (!empty($request->file('invite_email'))){
                        if (($handle = fopen($request->invite_email, "r")) !== false){
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
                            while (($csv_get_data = fgetcsv($handle, 1000, ",")) !== false){
                                $invitee[] = $csv_get_data[$a];
                            }
                            fclose($handle);
                        }
                    }
                }else{
                    return  ['success' => false,'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('labels.labels_lab_tiufmbaf')];
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
            $invited_members=array();
            $request['invite_type']=$invite_type;
            foreach ($invitee as $key => $invite_email){
                if (filter_var(trim($invite_email), FILTER_VALIDATE_EMAIL)){
                    if(!MemberManagement::where(['module_id' => (int) $request->module_id, 'role' => $request->role, 'email' => trim($invite_email)])->exists()){
                        $user_data = User::select('id')->where(['email'=>$invite_email])->first();
                        if(isset($user_data->id)){
                           $member_management_data['invitee_id']    = $user_data->id;
                        }
                        $member_management_data['type']             = $request->type;
                        $member_management_data['invite_type']      = $request->invite_type;
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
            if($invited_members){
            $result=$this->member_mangement->create($invited_members);
            if($result){
                $response = ['success' => true,'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_edas')];
            return $response;    
            }else{
                $response = ['success' => false, 'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_ntcedad')];
                return $response; 
            }
        }else{
            $response = ['success' => false, 'already_exist_email_data' => $already_exists_data, 'invalid_email_data' => $invalid_email_data, 'message' => __('notification.notification_ntcedad')];
            return $response;  
        }
        } catch (\Exception $e) {
            return false;
        }
    }

}