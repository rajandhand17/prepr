<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationMember;
use Exception;

class OrganizationMemberService
{
    public static function updateOrganizationMember($request, $org_id)
    {
        try {
            $people = OrganizationMember::where('organization_id', $org_id)->forceDelete();

            if (!empty(array_filter($request->people_name))) {
                foreach ($request->people_name as $key => $value) {
                    $people = new OrganizationMember();
                    $image = '';
                    $aws = env('AWS_URL');
                    if (isset($request->file('image')[$key])) {
                        if ($request->file('image')[$key]) {
                            $file = $request->file('image')[$key];
                            $image = $file->store('uploads/people', 's3');
                            $image = str_replace($aws, '', $image);
                            $people->image = $image;
                            $people->organization_id = $org_id;
                            $people->name = $value;
                            $people->description = $request->people_des[$key];
                            $people->save();
                        }
                    } else {
                        $people->name = $value;
                        $people->description = $request->people_des[$key];
                        $people->organization_id = $org_id;
                        $image = $request->image[$key];
                        $image = str_replace($aws, '', $image);
                        $people->image = $image;
                        $people->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteOrganizationMember($id)
    {
        try {
            $deleteOrgMembers = OrganizationMember::where('organization_id', $id)->delete();

            if (!$deleteOrgMembers) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function createOrganizationMember($request, $orgId)
    {
        try {
            if (!empty(array_filter($request->people_name))) {
                foreach ($request->people_name as $key => $value) {
                    $image = '';
                    if (isset($request->image[$key])) {
                        $image = $request->image[$key]->store('uploads/people', 's3');
                    } else {
                        $image = '';
                    }

                    $people_data = [
                        'organization_id' => $orgId,
                        'name'            => $value,
                        'description'     => $request->people_des[$key],
                        'image'           => $image,
                    ];
                    $people = OrganizationMember::create($people_data);
                }
            }

            return $people;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getOrganizationMembersById($orgId)
    {
        try {
            $people = OrganizationMember::where('organization_id', $orgId)->get();

            return $people;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
