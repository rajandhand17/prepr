<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\PlanSubscriptionHelper;
use App\Helpers\UtilityHelper;
use App\Models\Organization;
use Illuminate\Http\Request;
use DB;
use App\Models\OrganizationMember;
class OrganizationService
{
    public function checkOrganizationExist(Request $request)
    { 
        try {
        $organization_exists = Organization::select('id')->where('name', $request->name)->withTrashed()->first();
        if ($organization_exists == null) {
            return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
    }

    public function checkOrganizationExistInTrash(Request $request)
    {  
        try {
        $organization_trashed_exists = Organization::select('id')->where('name', $request->name)->onlyTrashed()->first();
        if ($organization_trashed_exists == null) {
            return true;
        }
        return false;
        } catch (\Exception $e) {
           return false;
        }
    }

    public function uploadOrganizationProfileImage(Request $request)
    {   
        try {
            $profile_image_path = FileUploadHelper::uploadbase64ImageToS3($request->profile_image, 'organization');
            if ($profile_image_path == false) {
                return false;
            }
            return $profile_image_path;   
        }catch (\Exception $e){
            return false;
        }
    }

    public function uploadOrganizationCoverImage(Request $request)
    {  try {
        $cover_image_path = FileUploadHelper::uploadImageToS3($request->cover_image, 'organization');
        if ($cover_image_path == false) {
            return false;
        }
        return $cover_image_path;
    } catch (\Exception $e) {
        return false;
    }
    }

    public function createOrganization(Request $request, $profile_image_path, $cover_image_path)
    {  
        try {
        //DB::beginTransaction();
        $model = new Organization();
        $organization = new Organization();
        $organization->language = ($request->has('language')) ? $request->language : 'en';
        $organization->user_id = $request->user_id;
        $organization->name = $request->name;
        $organization->description = ($request->has('description')) ? $request->description : null;
        $organization->slug = UtilityHelper::generateSlug($request->name, $model);
        $organization->cover_image = $cover_image_path;
        $organization->profile_image = $profile_image_path;
        $organization->website = ($request->has('website')) ? $request->website : null;
        $organization->about = ($request->has('about')) ? $request->about : null;
        $organization->category = $request->category;
        if ($request->status !== null){
        $organization->status = $request->status;
        }
        $organization->total_employees = $request->total_employees;
        if($organization->save()){
         //   DB::commit();
        $response=$organization; 
        return $response;
        }else{
            //::rollback();
            $response= ['success' => false, 'message' => __('responses.create_organization_failed')];
            return $response;
        }
        } catch (\Exception $e) {
           // DB::rollback();
            $response= ['success' => false, 'message' => __('responses.send_error')];
            return $response;
        }
    }

    public function subscribePlan($organization)
    {   
        try {
            $planSubscribed="";
            $cust_id = PlanSubscriptionHelper::getCustomer(auth()->user()->email);
            if ($cust_id != []) {
                $planSubscribed = PlanSubscriptionHelper::subscribePlan($cust_id, 'free-plan-CAD-Yearly', $organization->id);
            }
    
            return  $planSubscribed;
        } catch (\Exception $e) {
        return false;
        }
    }

    public function organizationMemeber($people,$organization_id){
        try{ 
            $add_members=json_decode($people);
            foreach ($add_members as $key => $value) {
                $people=new OrganizationMember;
                $people->name=$value->name;
                $people->organization_id=$organization_id;    
                $people->description=$value->description;
                $image=FileUploadHelper::uploadbase64ImageToS3($value->image,"organization");
                $people->image=$image;
                if(!$people->save()){
                   DB::rollback();
                }
           }
        } catch (\Exception $e) {
            return false;
        }
    }
}
