<?php
namespace App\Repositories\Api\Organization;

Interface OrganizationInterface{
   
    public function view($slug,$language);
    public function create($request);
    public function update($slug,$language);
    public function delete($language,$slug);
    public function list($language);
}