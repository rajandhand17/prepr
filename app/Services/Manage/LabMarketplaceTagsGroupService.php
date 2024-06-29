<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabMarketplaceTagsGroups;
use App\Models\LabTagsGroups;
use Exception;

class LabMarketplaceTagsGroupService
{
    public function addLabMarketplaceTagsGroup($labTemplateId, $labId)
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
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function redeemLabMarketplaceTagsGroup($redeemLabId, $labMarketplaceId)
    {
        try {
            $labMarketplaceTagsGroupData = LabMarketplaceTagsGroups::where('lab_marketplace_id', $labMarketplaceId)->get();
            if (!empty($labMarketplaceTagsGroupData)) {
                foreach ($labMarketplaceTagsGroupData as $labMarketplaceTagsGroup) {
                    $newLabTagsGroup = new LabTagsGroups();
                    $newLabTagsGroup->lab_id = $redeemLabId;
                    $newLabTagsGroup->foreign_id = $labMarketplaceTagsGroup->foreign_id;
                    $newLabTagsGroup->type = $labMarketplaceTagsGroup->type;
                    $newLabTagsGroup->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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
            UtilityHelper::logError($e);
            return false;
        }
    }
}
