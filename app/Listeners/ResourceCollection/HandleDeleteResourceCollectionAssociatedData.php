<?php

namespace App\Listeners\ResourceCollection;

use App\Events\ResourceCollection\DeleteResourceCollectionAssociatedData;
use App\Events\ResourceModule\DeleteResourceModuleAssociatedData;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceCollectionSkillsGroupsStackService;
use App\Services\Manage\ResourceCollectionTagsGroupsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
            $componentAssociation = ComponentAssociationService::deleteResourceCollectionAssociation($resourceCollectionId);
            return $componentAssociation;

            if (!$componentAssociation) {
                return false;
            }
            $resourceCollectionSkillsGroupStack=ResourceCollectionSkillsGroupsStackService::deleteResourceCollectionSkillsGroupsStack($resourceCollectionId);
            if(!$resourceCollectionSkillsGroupStack){
                return false;
            }
            $resourceCollectionTagsGroups=ResourceCollectionTagsGroupsService::deleteResourceCollectionTagsGroups($resourceCollectionId);
            if(!$resourceCollectionTagsGroups){
                return false;
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }
}
