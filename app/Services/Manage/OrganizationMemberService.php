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
                // Fetch existing members
                $existingMembers = OrganizationMember::where('organization_id', $organization_id)
                    ->get(['name', 'position'])
                    ->keyBy(function ($item) {
                        return $item->name.':'.$item->position;
                    });
                // Prepare request members by name and position
                $requestMembers = [];
                foreach ($request->organization_members as $value) {
                    $key = $value['name'].':'.$value['position'];
                    $requestMembers[$key] = $value;
                }
                // Determine which existing members are no longer in the request
                $membersToDelete = array_diff(array_keys($existingMembers->toArray()), array_keys($requestMembers));

                // Delete members that are no longer in the request
                if (!empty($membersToDelete)) {
                    OrganizationMember::where('organization_id', $organization_id)
                        ->whereIn(DB::raw("CONCAT(name, ':', position)"), $membersToDelete)
                        ->delete();
                }
                $recordsToInsert = [];
                // Process each member from the request
                foreach ($request->organization_members as $value) {
                    $existingRecord = OrganizationMember::where('organization_id', $organization_id)
                        ->where('name', $value['name'])
                        ->where('position', $value['position'])
                        ->exists();
                    // Insert the record only if it does not exist
                    if (!$existingRecord) {
                        $image = isset($value['image']) ? FileUploadHelper::uploadImageToS3($value['image'], 'organization') : config('site-settings.default_user_profile_image');

                        $recordsToInsert[] = [
                            'organization_id' => $organization_id,
                            'name'            => $value['name'],
                            'position'        => $value['position'],
                            'image'           => $image,
                        ];
                    }
                }
                // Bulk insert the new records
                if (!empty($recordsToInsert)) {
                    OrganizationMember::insert($recordsToInsert);
                }
                DB::commit();

                return true;
            }
            $deleteMembers=self::deleteOrganizationMembers($organization_id);
            if($deleteMembers){
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
