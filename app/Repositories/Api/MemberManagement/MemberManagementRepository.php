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
    public function index($component,$slug,$request)
    {  
        try{
            return $this->member_mangement->index($component,$slug,$request);
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
        try { 
            return $this->member_mangement->create($component,$slug,$request);
           
        } catch (\Exception $e) {
            return false;
        }
    }

}