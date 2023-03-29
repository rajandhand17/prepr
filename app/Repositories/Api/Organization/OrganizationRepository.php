<?php
namespace App\Repositories\Api\Organization;
use App\Models\Organization;
class OrganizationRepository implements OrganizationInterface{
    private $organization;
    public function __construct(Organization $organization)
    {
        $this->organization=$organization;
    }

    public function view($slug,$language)
    {
        try{
            return $this->organization->view($slug,$language);
         }catch (\Exception $e){
            return false;
         }

    }

    public function list($language)
    {
        try {
         return $this->organization->list($language);
        } catch (\Exception $e) {
        return false;
        }
    }   
    public function delete($slug,$request)
    {
        try{
            return $this->organization->delete($slug,$request);

         }catch (\Exception $e){
            return false;
         }
    }

    public function create($request)
    {
        try {
            return $this->organization->create($request);
           
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($slug,$language)
    {    
        try {
            return $this->organization->updates($slug,$language);

        } catch (\Exception $e) {
            return false;
        }
    }
   

}