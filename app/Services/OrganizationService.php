<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\PlanSubscriptionHelper;
use App\Helpers\UtilityHelper;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationService
{
    public function checkOrganizationExist(Request $request)
    {
        $organization_exists = static::select('id')->where('name', $request->name)->withTrashed()->first();
        if ($organization_exists == null) {
            return true;
        }

        return false;
    }

    public function checkOrganizationExistInTrash(Request $request)
    {
        $organization_trashed_exists = static::select('id')->where('name', $request->name)->onlyTrashed()->first();
        if ($organization_trashed_exists == null) {
            return true;
        }

        return false;
    }

    public function uploadOrganizationProfileImage(Request $request)
    {
        $profile_image_path = FileUploadHelper::uploadbase64ImageToS3($request->profile_image, 'organization');
        if ($profile_image_path == false) {
            return false;
        }

        return $profile_image_path;
    }

    public function uploadOrganizationCoverImage(Request $request)
    {
        $cover_image_path = FileUploadHelper::uploadImageToS3($request->cover_image, 'organization');
        if ($cover_image_path == false) {
            return false;
        }

        return $cover_image_path;
    }

    public function createOrganization(Request $request, $profile_image_path, $cover_image_path)
    {
        DB::beginTransaction();
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
        if ($request->status !== null) {
            $organization->status = $request->status;
        }
        $organization->total_employees = $request->total_employees;
        $organization->save();

        return $organization;
    }

    public function subscribePlan($organization)
    {
        $cust_id = PlanSubscriptionHelper::getCustomer(auth()->user()->email);
        if ($cust_id != []) {
            $planSubscribed = PlanSubscriptionHelper::subscribePlan($cust_id, 'free-plan-CAD-Yearly', $organization->id);
        }

        return  $planSubscribed;
    }
}
