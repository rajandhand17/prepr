<?php
namespace App\Repositories\Api\MemberManagement;
use App\Repositories\Api\MemberManagement\MemberManagementInterface;
use AWS\CRT\HTTP\Request;
use App\Models\MemberManagement;

class MemberManagementRepository implements MemberManagementInterface{
    
    private $member_mangement;
   function __construct(MemberManagement $member_mangement)
   {
      $this->member_mangement=$member_mangement;
   }
    public function view($language)
    {  
        try{
            return $this->member_mangement->view($language);
         }
         catch (\Exception $e){
            return false;
         }   
    }

    public function delete($slug,$language)
    {
        try {
            return $this->member_mangement->deletes($slug,$language);
        }catch(\Exception $e) {
            return $e;
        }
    }
    
    public function deleteMultiple($slug)
    {
        try {
            return $this->member_mangement->deleteMultiple($slug);
        } catch (\Exception $e) {
            return $e;
        }
    }
}