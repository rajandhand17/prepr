<?php
namespace App\Repositories\Api\MemberManagement;

Interface MemberManagementInterface{
   
    public function index($component,$slug);
    public function deleteMultiple($component,$slug,$request);
    public function create($component,$slug,$request);
}