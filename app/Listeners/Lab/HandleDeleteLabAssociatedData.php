<?php

namespace App\Listeners\Lab;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabAcheivementService;
use App\Services\Manage\LabAddressService;
use App\Services\Manage\LabExternalLinksService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;
use App\Services\Public\FeaturedModuleService;

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
    public function handle(DeleteLabAssociatedData $event)
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
            $featuredModule = FeaturedModuleService::deleteFeaturedModule('0',$lab_id);
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
