<?php

namespace App\Listeners\ResourceModule;

use App\Events\ResourceModule\DeleteResourceModuleAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ResourceModuleDetailService;
use App\Services\Manage\ResourceModuleRatingService;
use App\Services\Manage\ResourceModuleSkillsGroupsStackService;
use App\Services\Manage\ResourceModuleTagsGroupsService;
use App\Services\Public\FeaturedModuleService;

class HandleDeleteResourceModuleAssociatedData
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
    public function handle(DeleteResourceModuleAssociatedData $event)
    {
        try {
            $resourceModuleId = $event->resourceModuleId;
            $deleteResourceModuleDetail = ResourceModuleDetailService::deleteResourceModuleDetail($resourceModuleId);
            if (!$deleteResourceModuleDetail) {
                return false;
            }
            $deleteResourceModuleDetailVisit = ResourceModuleDetailService::deleteResourceModuleDetailVisit($resourceModuleId);
            if (!$deleteResourceModuleDetailVisit) {
                return false;
            }
            $deleteResourceModuleSkillsGroupsStack = ResourceModuleSkillsGroupsStackService::deleteResourceModuleSkillsGroupsStack($resourceModuleId);
            if (!$deleteResourceModuleSkillsGroupsStack) {
                return false;
            }
            $deleteResourceModuleTagsGroups = ResourceModuleTagsGroupsService::deleteResourceModuleTagsGroups($resourceModuleId);
            if (!$deleteResourceModuleTagsGroups) {
                return false;
            }
            $deleteResourceModuleRating = ResourceModuleRatingService::deleteResourceModuleRating($resourceModuleId);
            if (!$deleteResourceModuleRating) {
                return false;
            }
            $featuredModule = FeaturedModuleService::deleteFeaturedModule('4', $resourceModuleId);
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
