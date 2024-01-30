<?php

namespace App\Listeners\Lab;

use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabAcheivementService;
use App\Services\Manage\LabAddressService;
use App\Services\Manage\LabExternalLinksService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;

class HandleDeleteLabAssociatedData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param
     *
     * @return void
     */
    public function handle(DeleteLabMarketplaceAssociatedData $event)
    {
        try {
            $lab_id = $event->labId;
            $componentAssociation = ComponentAssociationService::deletelabAssociation($lab_id);
            if (!$componentAssociation) {
                return false;
            }
            $deleteLabAchievement = LabAcheivementService::deleteLabAchievement($lab_id);
            if (!$deleteLabAchievement) {
                return false;
            }
            $labExternalLinks = LabExternalLinksService::deleteLabExternalLinks($lab_id);
            if (!$labExternalLinks) {
                return false;
            }
            $labTagGroups = LabTagsGroupsService::deleteLabTagsGroups($lab_id);

            if (!$labTagGroups) {
                return false;
            }
            $labSkillsGroupsService = LabSkillsGroupsStackService::deleteLabSkillsGroupsStack($lab_id);
            if (!$labSkillsGroupsService) {
                return false;
            }
            $labAddress = LabAddressService::deleteLabAddress($lab_id);

            if (!$labAddress) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
