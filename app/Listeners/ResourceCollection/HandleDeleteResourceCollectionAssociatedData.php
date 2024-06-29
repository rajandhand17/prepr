<?php

namespace App\Listeners\ResourceCollection;

use App\Events\ResourceCollection\DeleteResourceCollectionAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceCollectionSkillsGroupsStackService;
use App\Services\Manage\ResourceCollectionTagsGroupsService;

class HandleDeleteResourceCollectionAssociatedData
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
    public function handle(DeleteResourceCollectionAssociatedData $event)
    {
        try {
            $resourceCollectionId = $event->resourceCollectionId;
            $resourceCollectionTagsGroups = ResourceCollectionTagsGroupsService::deleteResourceCollectionTagsGroups($resourceCollectionId);
            if (!$resourceCollectionTagsGroups) {
                return false;
            }
            $resourceCollectionSkillsGroupStack = ResourceCollectionSkillsGroupsStackService::deleteResourceCollectionSkillsGroupsStack($resourceCollectionId);
            if (!$resourceCollectionSkillsGroupStack) {
                return false;
            }
            $componentAssociation = ComponentAssociationService::deleteResourceCollectionAssociation($resourceCollectionId);
            if (!$componentAssociation) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
