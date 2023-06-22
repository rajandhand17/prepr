<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\OrganizationMember;
use DB;

class OrganizationMemberService
{
    public static function organizationAddMemeber($request, $organization_id)
    {
        try {
            if (isset($request->organization_members) && !empty($request->organization_members)) {
                DB::beginTransaction();
                foreach ($request->organization_members as $value) {
                    $organization_member = new OrganizationMember();
                    $organization_member->organization_id = $organization_id;
                    $organization_member->name = $value['name'];
                    $organization_member->position = $value['position'];
                    $image = isset($value['image']) ? FileUploadHelper::uploadImageToS3($value['image'], 'organization') : null;
                    $organization_member->image = $image;
                    $organization_member->save();
                }
                DB::commit();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
