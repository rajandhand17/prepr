<?php
namespace App\Repositories\Api\Organization;

Interface OrganizationInterface{
   
    public function list($request);
    public function view($request);
    public function create($request);
    public function update($request);
    public function delete($request);
    
}