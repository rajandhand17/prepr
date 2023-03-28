<?php
namespace App\Repositories\Api\Organization;
use App\Models\Organization;
class OrganizationRepository implements OrganizationInterface{
    private $organization;
    public function __construct(Organization $organization)
    {
        $this->organization=$organization;
    }

    public function list($language,$slug)
    {
        try{
            return $this->organization->list($language,$slug);
         }catch (\Exception $e){
            return false;
         }

    }

    public function delete($request)
    {
        try{
            return $this->organization->delete($request->language,$request->slug);

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

    public function update($language,$slug,$request)
    {    
        try {
            return $this->organization->updates($language,$slug,$request);

        } catch (\Exception $e) {
            return false;
        }
    }

}