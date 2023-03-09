<?php
namespace App\Repositories\Api\Organization;

Interface OrganizationInterface{
   
    public function getOrganization($request);
    public function view($request);
    public function create($request);
    public function update($request);
    public function delete($request);
    
}