<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabMarketplaceTagsGroups;
use App\Models\LabTagsGroups;
use Exception;

class LabMarketplaceTagsGroupsService
{
    public static function addLabMarketplaceTagsGroup($labTemplateId, $labId)
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
}
