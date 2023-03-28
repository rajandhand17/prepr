<?php
namespace App\Repositories\Api\Organization;

Interface OrganizationInterface{
   
    public function list($language,$slug);
    public function create($request);
    public function update($language,$slug,$request);
    public function delete($request);
}