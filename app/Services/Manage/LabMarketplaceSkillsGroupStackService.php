<?php

namespace App\Services\Manage;

use App\Models\LabMarketplaceSkillsGroupsStack;
use App\Models\LabSkillsGroupsStack;
use Exception;

class LabMarketplaceSkillsGroupStackService
{
    public function addLabMarketplaceSkillsGroupsStack($labMarketplaceId, $labId)
    {
        try {
            $exisingLabSkillsGroupsStack = LabSkillsGroupsStack::where('lab_id', $labId)->get();
            if ($exisingLabSkillsGroupsStack) {
                foreach ($exisingLabSkillsGroupsStack as $skillsGroup) {
                    $labTemplateSkillsGroupStack = new LabMarketplaceSkillsGroupsStack();
                    $labTemplateSkillsGroupStack->lab_marketplace_id = $labMarketplaceId;
                    $labTemplateSkillsGroupStack->foreign_id = $skillsGroup->foreign_id;
                    $labTemplateSkillsGroupStack->type = $skillsGroup->type;
                    $labTemplateSkillsGroupStack->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function redeemLabMarketplaceSkillsGroupsStack($redeemLabId, $labMarketplaceId)
    {
        try {
            $labMarketplaceSKillsGroupStackData = LabMarketplaceSkillsGroupsStack::where('lab_marketplace_id', $labMarketplaceId)->get();
            if (!empty($labMarketplaceSKillsGroupStackData)) {
                foreach ($labMarketplaceSKillsGroupStackData as $labMarketplaceSKillsGroupStack) {
                    $newLabSKillsGroupStack = new LabSkillsGroupsStack();
                    $newLabSKillsGroupStack->lab_id = $redeemLabId;
                    $newLabSKillsGroupStack->foreign_id = $labMarketplaceSKillsGroupStack->foreign_id;
                    $newLabSKillsGroupStack->type = $labMarketplaceSKillsGroupStack->type;
                    $newLabSKillsGroupStack->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteLabMarketplaceSkillsGroupStackService($labMarketplaceId)
    {
        try {
            $checkLabMarketplaceSKillsGroupStackService = LabMarketplaceSkillsGroupsStack::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($checkLabMarketplaceSKillsGroupStackService) {
                $deleteLabMarketplaceSKillsGroupStackService = LabMarketplaceSkillsGroupsStack::where('lab_marketplace_id', $labMarketplaceId)->delete();
                if (!$deleteLabMarketplaceSKillsGroupStackService) {
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
