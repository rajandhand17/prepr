<?php

namespace App\Listeners\LabMarketplace;

use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\LabMarketplaceAchievementsService;
use App\Services\Manage\LabMarketplaceAddressService;
use App\Services\Manage\LabMarketplaceComponentAssociationService;
use App\Services\Manage\LabMarketplaceExternalLinksService;
use App\Services\Manage\LabMarketplaceSkillsGroupStackService;
use App\Services\Manage\LabMarketplaceTagsGroupService;
use App\Services\Manage\LabService;

class HandleDeleteLabMarketplaceAssociatedData
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
     *
     * @param \App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData $event
     *
     * @return void
     */
    public function handle(DeleteLabMarketplaceAssociatedData $event)
    {
        try {
            $labMarketplaceId = $event->labMarketplaceId;
            $deleteLabMarketplace = LabMarketplaceAddressService::deleteLabMarketplaceAddress($labMarketplaceId);
            if (!$deleteLabMarketplace) {
                return false;
            }

            $deleteLabMarketplaceSkillsGroupsStack = LabMarketplaceSkillsGroupStackService::deleteLabMarketplaceSkillsGroupStackService($labMarketplaceId);
            if (!$deleteLabMarketplaceSkillsGroupsStack) {
                return false;
            }

            $deleteLabMarketplaceTagsGroupsStack = LabMarketplaceTagsGroupService::deleteLabMarketplaceTagsGroup($labMarketplaceId);
            if (!$deleteLabMarketplaceTagsGroupsStack) {
                return false;
            }

            $deleteLabMarketplaceExternalLinks = LabMarketplaceExternalLinksService::deleteLabMarketplaceExternalLink($labMarketplaceId);
            if (!$deleteLabMarketplaceExternalLinks) {
                return false;
            }

            $deleteLabMarketplaceAchievement = LabMarketplaceAchievementsService::deleteLabMarketplaceAchievement($labMarketplaceId);
            if (!$deleteLabMarketplaceExternalLinks) {
                return false;
            }

            $deleteLabMarketplaceComponentAssociation = LabMarketplaceComponentAssociationService::deleteLabMarketplaceComponentAssociation($labMarketplaceId);
            if (!$deleteLabMarketplaceComponentAssociation) {
                return false;
            }

            $labMarketplaceUpdatePreBuilt = LabService::labMarketplaceUpdatePreBuilt($labMarketplaceId);
            if (!$labMarketplaceUpdatePreBuilt) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
