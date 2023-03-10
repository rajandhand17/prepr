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
            return $this->organization->create($request->language,$request->user_id,$request->name,$request->display_name,$request->description, $request->profile_image, $request->cover_image,$request->website,$request->about,$request->category, $request->status, $request->total_employees,$request->latitude,$request->longitude,$request->address,$request->city,$request->state,$request->country,$request->zip_code);
           
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($request)
    {
        try {
            return $this->organization->updates($request->language,$request->slug,$request->name,$request->description, $request->profile_image, $request->cover_image,$request->website,$request->about,$request->category, $request->status, $request->total_employees,$request->organization_id, $request->latitude, $request->longitude, $request->address, $request->city, $request->state, $request->country, $request->zipcode);

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