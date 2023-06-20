<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\PlanSubscriptionHelper;
use App\Helpers\UtilityHelper;
use App\Models\Organization;
use DB;
class OrganizationService
{
    public function checkOrganizationExist($request)
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

    public function checkOrganizationExistInTrash($request)
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

    public function uploadOrganizationProfileImage($request)
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

    public function uploadOrganizationCoverImage($request)
    {  try {
        $cover_image_path = FileUploadHelper::uploadbase64ImageToS3($request->cover_image, 'organization');
        if ($cover_image_path == false) {
            return false;
        }
        return $cover_image_path;
    } catch (\Exception $e) {
        return false;
    }
    }

    public function createOrganization($request, $profile_image_path, $cover_image_path)
    {  
        try {
        DB::beginTransaction();
        $model = new Organization();
        $organization = new Organization();
        $organization->language = isset($request->language) ? $request->language : 'en';
        $organization->user_id = $request->user_id;
        $organization->name = $request->name;
        $organization->description = isset($request->description) ? $request->description : null;
        $organization->slug = UtilityHelper::generateSlug($request->name, $model);
        $organization->cover_image = $cover_image_path;
        $organization->profile_image = $profile_image_path;
        $organization->website = isset($request->website) ? $request->website : null;
        $organization->about = isset($request->about) ? $request->about : null;
        $organization->category = $request->category;
        if ($request->status !== null){
        $organization->status = $request->status;
        }
        $organization->total_employees = $request->total_employees;
        if($organization->save()){
            DB::commit();
        $response=$organization; 
        return $response;
        }else{
            DB::rollback();
           return false;
        }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateOrganization($request,$cover_images_path,$profile_images_path,$slug){
        try{
           $organization=Organization::select('id','language','name','slug','description','cover_image','profile_image', 'website' ,'about', 'category', 'status', 'is_verified','total_employees')->where("slug",$slug)->first();
            if($organization!==null){
            $organization->language=($request->has('language')) ?$request->language : $organization->language;
            $organization->name=($request->has('name')) ?$request->name : $organization->name;
            $organization->display_name=($request->has('display_name')) ?$request->display_name : $organization->display_name;
            $organization->description=($request->has('description')) ?$request->description : $organization->description;
            $organization->cover_image=$cover_images_path?$cover_images_path:$organization->cover_image;
            $organization->profile_image=$profile_images_path?$profile_images_path:$organization->profile_image;
            $organization->website=($request->has('website'))?$request->website:$organization->website;
            $organization->about=($request->has('about'))?$request->about:$organization->about;
            $organization->category=($request->has('category'))?$request->category:$organization->category;
            $organization->status=($request->has('status'))?$request->status:$organization->status;
            $organization->total_employees=($request->has('total_employees'))?$request->total_employees:$organization->total_employees;
            $organization->save();
            if($organization){
                $request->organization_id=$organization->id;
                    return $organization;
            }else{
                $response= ['success' => false, 'message' => __('responses.updated_organization_failed')];
                    return $response;
            }
            }
    
            $response= ['success' => false, 'message' => __('responses.organization_not_exists')];
                     return $response;
           }catch (\Exception $e) {
            return $e;
                $response= ['success' => false, 'message' => __('responses.send_error')];
                return $response;
           }
    }
    
}
