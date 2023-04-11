<?php
namespace App\Repositories\Api\MemberManagement;
use App\Repositories\Api\MemberManagement\MemberManagementInterface;
use AWS\CRT\HTTP\Request;
use App\Models\MemberManagement;
use App\Models\MemberManagementCsvUpload;

class MemberManagementRepository implements MemberManagementInterface{
    
    private $member_mangement;
    private $member_mangement_csv;
   function __construct(MemberManagement $member_mangement,MemberManagementCsvUpload $member_mangement_csv)
   {
      $this->member_mangement=$member_mangement;
      $this->member_mangement_csv=$member_mangement_csv;
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

    public function uploadCsv($component,$slug,$request)
    {
        try {
            return $this->member_mangement_csv->uploadCsv($component,$slug,$request);
        } catch (\Exception $e){
            return false;
        }
    }
}