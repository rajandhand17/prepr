<?php
namespace App\Repositories\Api\Organization;

Interface OrganizationInterface{
   
    public function getOrganization($request);
    public function viewOrganization($request);
    public function createOrganization($request);
    public function updateOrganization($request);
    public function deleteOrganization($request);
    
}