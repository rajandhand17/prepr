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

    public function deleteOrganization($request)
    {
        try{
            return $this->organization->deleteOrganization($request->language,$request->organization_id);

         }catch (\Exception $e){
            return false;
         }
    }

    public function createOrganization($request)
    {
        try {
            return $this->organization->createOrganization($request->language,$request->user_id,$request->name,$request->display_name,$request->description, $request->profile_image, $request->cover_image,$request->website,$request->about,$request->category, $request->status, $request->total_employees);
           
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganization($request)
    {
        try {
            return $this->organization->updateOrganization($request->language,$request->organization_id,$request->user_id,$request->name,$request->display_name,$request->description, $request->profile_image, $request->cover_image,$request->website,$request->about,$request->category, $request->status, $request->total_employees);

           
        } catch (\Exception $e) {
            return false;
        }
    }

    public function viewOrganization($request)
    {
        try {
            return $this->organization->viewOrganization($request->language,$request->slug);
        } catch (\Exception $e) {
            return false;
        }
    }

}