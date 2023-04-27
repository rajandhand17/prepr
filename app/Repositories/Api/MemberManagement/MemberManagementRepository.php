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
            $not_inserted_emails=[];
            $inserted_emails=[];
            $user_email=[];
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
                    if($request->invite_type=="email"){
                        foreach ($invitee as $invite_member){
                            if(filter_var(trim($invite_member), FILTER_VALIDATE_EMAIL)){
                                $user_email[]=$invite_member;
                            }else{
                                $invalid_email_data[]=$invite_member;
                            }
                        }
                        $request['invite_type']="0";
                    }
                    if($request->invite_type == 'network'){
                        $request['invite_type']="1";
                        foreach ($invitee as $invite_member){
                            $user_data = User::select('email')->where(['id' => (int) $invite_member])->first();
                            if($user_data && filter_var(trim($user_data->email), FILTER_VALIDATE_EMAIL)){
                                $user_email[]=$user_data->email;
                            }
                        }
                    }
        }
            if($request->invite_type == 'csv'){
                $request['invite_type']="3";
                /**get records from csv */
                $user_email=$this->member_mangement->getRecordsFromCsv($request);
                if(!$user_email){
                    return false;
                }
            }
            foreach ($user_email as $key => $email) {
                $result=$this->member_mangement->create($email,$request);
                if($result==true){
                    $inserted_emails[]=$email;
                }
                if($result==false){
                    $not_inserted_emails[]=$email;
                }
            }
            if(!empty($inserted_emails)){
              return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

}