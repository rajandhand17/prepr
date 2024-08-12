<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\OrganizationMember;
use DB;

class OrganizationMemberService
{
    public static function createOrganizationMembers($request, $organization_id)
    {
        try {
            DB::beginTransaction();
            if (isset($request->organization_members) && !empty($request->organization_members)) {
                foreach ($request->organization_members as $value) {
                    $organization_member = new OrganizationMember();
                    $organization_member->organization_id = $organization_id;
                    $organization_member->name = $value['name'];
                    $organization_member->position = $value['position'];
                    $image = isset($value['image']) ? FileUploadHelper::uploadImageToS3($value['image'], 'organization') : config('site-settings.default_user_profile_image');
                    $organization_member->image = $image;
                    $organization_member->save();
                }
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public static function updatesOrganizationMembers($request, $organization_id)
    {
        try {
            DB::beginTransaction();
            if (isset($request->organization_members) && !empty($request->organization_members)) {
                OrganizationMember::where('organization_id', $organization_id)->delete();
                foreach ($request->organization_members as $value) {
                    $organization_member = new OrganizationMember();
                    $organization_member->organization_id = $organization_id;
                    $organization_member->name = $value['name'];
                    $organization_member->position = $value['position'];
                    $image = isset($value['image']) ? FileUploadHelper::uploadImageToS3($value['image'], 'organization') : config('site-settings.default_user_profile_image');
                    $organization_member->image = $image;
                    $organization_member->save();
                }
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public static function deleteOrganizationMembers($organizationId)
    {
        try {
            $organizationMemberIds = OrganizationMember::where('organization_id', $organizationId)->pluck('id');
            if (!empty($organizationMemberIds)) {
                $deleteOrganizationMember = OrganizationMember::whereIn('id', $organizationMemberIds)->delete();
                if ($deleteOrganizationMember) {
                    return true;
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
