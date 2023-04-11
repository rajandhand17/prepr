<?php
namespace App\Repositories\Api\MemberManagement;

Interface MemberManagementInterface{
   
    public function index($component,$slug,$request);
    public function delete($component,$slug,$request);
    public function create($component,$slug,$request);
    public function uploadCsv($component,$slug,$request);
}