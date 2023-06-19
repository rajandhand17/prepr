<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\OrganizationMember;

class OrganizationMemberService
{
    public function organizationAddMemeber($people, $organization_id)
    {
        try {
            foreach ($people as $key => $value) {
                $people = new OrganizationMember();
                $people->name = $value['name'];
                $people->organization_id = $organization_id;
                $people->description = $value['description'];
                $image = isset($value['image']) ? FileUploadHelper::uploadbase64ImageToS3($value['image'], 'organization') : null;
                $people->image = $image;
                if ($people->save()) {
                    return true;
                }

                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
