<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\MemberManagement;
use App\Models\OrganizationMember;
use DB;
use App\Models\Organization;
class OrganizationMemberService
{
    public function organizationAddMemeber($request, $organization_id)
    {
        try {
            if (isset($request->organization_members) && !empty($request->organization_members)) {
                DB::beginTransaction();
                foreach ($request->organization_members as $key => $value) {
                    $people = new OrganizationMember();
                    $people->name = $value['name'];
                    $people->organization_id = $organization_id;
                    $people->description = $value['description'];
                    $image = isset($value['image']) ? FileUploadHelper::uploadImageToS3($value['image'], 'organization') : null;
                    $people->image = $image;
                    if (!$people->save()) {
                        DB::rollback();

                        return false;
                    }
                }
                DB::commit();

                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }
}
