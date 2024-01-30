<?php

namespace App\Services\Manage;

use App\Models\LabMarketplaceTagsGroups;
use App\Models\LabTagsGroups;
use Exception;

class LabMarketplaceTagsGroupService
{
    public function addLabMarketplaceTagsGroupsStack($labTemplateId, $labId)
    {
        try {
            $existingLabTagsGroupsStack = LabTagsGroups::where('lab_id', $labId)->get();

            if ($existingLabTagsGroupsStack) {
                foreach ($existingLabTagsGroupsStack as $existingLabTagGroup) {
                    $labMarketplaceTagsGroupStack = new LabMarketplaceTagsGroups();
                    $labMarketplaceTagsGroupStack->lab_marketplace_id = $labTemplateId;
                    $labMarketplaceTagsGroupStack->foreign_id = $existingLabTagGroup->foreign_id;
                    $labMarketplaceTagsGroupStack->type = $existingLabTagGroup->type;
                    $labMarketplaceTagsGroupStack->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteLabMarketplaceTagsGroup($labMarketplaceTagsGroupId)
    {
        try {
            $getLabMarketplaceTagsGroup = LabMarketplaceTagsGroups::where('lab_marketplace_id', $labMarketplaceTagsGroupId)->first();
            if ($getLabMarketplaceTagsGroup) {
                $deleteLabMarketplaceTagsGroup = LabMarketplaceTagsGroups::where('lab_marketplace_id', $labMarketplaceTagsGroupId)->delete();
                if (!$deleteLabMarketplaceTagsGroup) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
