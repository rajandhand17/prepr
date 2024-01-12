<?php

namespace App\Services\Manage;

use App\Models\LabTagsGroups;
use App\Models\LabTemplateTagsGroups;

class LabMarketplaceTagsGroupService
{
    public function createLabMarketplaceTagsGroupsStack($labTemplateId, $lab)
    {
        try {
            $existingLabTagsGroupsStack = LabTagsGroups::where('lab_id', $lab->id)->get();

            if ($existingLabTagsGroupsStack) {
                foreach ($existingLabTagsGroupsStack as $existingLabTagGroup) {
                    $labMarketplaceTagsGroupStack = new LabTemplateTagsGroups();
                    $labMarketplaceTagsGroupStack->template_lab_id = $labTemplateId->id;
                    $labMarketplaceTagsGroupStack->foreign_id = $existingLabTagGroup->foreign_id;
                    $labMarketplaceTagsGroupStack->type = $existingLabTagGroup->type;
                    $labMarketplaceTagsGroupStack->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
