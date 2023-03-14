<?php
namespace App\Repositories\Api\Organization;
use App\Models\Organization;
class OrganizationRepository implements OrganizationInterface{
    private $organization;
    public function __construct(Organization $organization)
    {
        $this->organization=$organization;
    }

    public function list($request)
    {
        try{
            return $this->organization->list($request->language,$request->search);
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

    public function update($request)
    {
        try {
            return $this->organization->updates($request);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function view($request)
    {
        try {
            return $this->organization->view($request->language,$request->slug);
        } catch (\Exception $e) {
            return false;
        }
    }

}