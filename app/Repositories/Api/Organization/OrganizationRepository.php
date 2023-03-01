<?php
namespace App\Repositories\Api\Organization;
use App\Models\Organization;
class OrganizationRepository implements OrganizationInterface{
    private $organization;
    public function __construct(Organization $organization)
    {
        $this->organization=$organization;
    }

    public function getOrganization($request)
    {
        try{
            return $this->organization->getOrganization($request->language,$request->search);
         }catch (\Exception $e){
            return false;
         }

    }

}