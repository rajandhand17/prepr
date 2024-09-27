<?php

namespace App\Listeners\ResourceGroup;

use App\Events\ResourceGroup\DeleteResourceGroupAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceGroupAchievementService;
use App\Services\Manage\ResourceGroupSkillsGroupsStackService;
use App\Services\Manage\ResourceGroupTagsGroupsService;
use App\Services\Public\FeaturedModuleService;

class HandleDeleteResourceGroupAssociatedData
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeleteResourceGroupAssociatedData $event)
    {
        try {
            $resourceGroupId = $event->resourceGroupId;
            $deleteResourceGroupDetail = ComponentAssociationService::deleteResourceGroupAssociation($resourceGroupId);
            if (!$deleteResourceGroupDetail) {
                return false;
            }
            $deleteResourceModuleSkillsGroupsStack = ResourceGroupSkillsGroupsStackService::deleteResourceGroupSkillsGroupsStack($resourceGroupId);
            if (!$deleteResourceModuleSkillsGroupsStack) {
                return false;
            }
            $deleteResourceModuleTagsGroups = ResourceGroupTagsGroupsService::deleteResourceGroupTagsGroups($resourceGroupId);
            if (!$deleteResourceModuleTagsGroups) {
                return false;
            }
            $deleteAchievementsGroups = ResourceGroupAchievementService::deleteResourceGroupAchievements($resourceGroupId);
            if (!$deleteAchievementsGroups) {
                return false;
            }
            $featuredModule = FeaturedModuleService::deleteFeaturedModule('6',$resourceGroupId);
            if (!$featuredModule) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
