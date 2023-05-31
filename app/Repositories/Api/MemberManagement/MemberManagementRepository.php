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
            $invalid_email_data=[];
            $invalid_users=[];
            $inserted_emails=[];
            $not_inserted_emails=[];
            $already_exists_emails=[];
            $request->type="0";
            $request->invite_status="0";
            $invitee = [];
            if($request->invite_type!="csv"){
                    if(gettype($request->invite_email)!="string"){
                        return false;
                    }
                    $invitee = explode(',', $request->invite_email);
                    if(!is_array($invitee)){
                        return false;
                    }
                    if($request->invite_type == 'email'){
                        $request->invite_type="0"; 
                    }
                    if($request->invite_type == 'network'){
                        $request->invite_type="1"; 
                    }
        }

            if($request->invite_type == 'csv'){
                $request['invite_type']="3";
                /**get records from csv */
                $invitee=$this->member_mangement->getRecordsFromCsv($request);
                if(!$invitee){
                    return false;
                }
            }
            /**convert string to array */
            foreach ($invitee as $invite_member){
                /**in case invite type email */
                if($request->invite_type == '1'){
                    $user_data = User::select('email')->where(['id' => (int) $invite_member])->first();
                    if($user_data==null){
                        $invalid_users[]=$invite_member;
                        continue;
                    }
                    $invite_member=$user_data->email;
                }
                if(filter_var(trim($invite_member), FILTER_VALIDATE_EMAIL)){
                    $result=$this->member_mangement->insert($invite_member,$request);
                   
                    if($result===true){
                        $inserted_emails[]=$invite_member;
                    }
                    if($result===false){
                         $not_inserted_emails[]=$invite_member;
                    }
                    if($result==="already_exists"){
                        $already_exists_emails[]=$invite_member;
                    }
                }else{
                    $invalid_email_data[]=$invite_member;
                }
            }
            $invalid_users=implode(",", $invalid_users);
            $response= new \stdClass;
            if(!empty($inserted_emails) || !empty($already_exists_emails)){
                 $response->status=true;
                 $response->already_exists_emails=$already_exists_emails;
                 $response->inserted_emails=$inserted_emails;
                 $response->invalid_email_data=$invalid_email_data;
                 if($request->invite_type == '1'){
                    $response->invalid_users=$invalid_users;
                 }
                 return $response;
            }
            $response->status=false;
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

}