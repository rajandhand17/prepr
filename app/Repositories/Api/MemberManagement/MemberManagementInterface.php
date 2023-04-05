<?php
namespace App\Repositories\Api\MemberManagement;

Interface MemberManagementInterface{
   
    public function view($language);
    public function delete($slug,$language);
    public function deleteMultiple($slug);
}