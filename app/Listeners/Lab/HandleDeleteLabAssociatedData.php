<?php

namespace App\Listeners\Lab;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Models\ComponentAssociation;
use App\Models\LabAcheivement;
use App\Models\LabAddress;
use App\Models\LabExternalLinks;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTagsGroups;
use App\Services\ComponentAssociationService;
use App\Services\LabAcheivementService;
use App\Services\LabAddressService;
use App\Services\LabExternalLinksService;
use App\Services\LabSkillsGroupsStackService;
use App\Services\LabTagsGroupsService;

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
     * @param \App\Events\Labs\DeleteLabAssociatedData $event
     *
     * @return void
     */
    public function handle(DeleteLabAssociatedData $event)
    {
        try {
            $lab_id = $event->labId;
            $componentAssociation =ComponentAssociationService::deletelabAssociation($lab_id);
            if(!$componentAssociation){
                return false;
            }
            $deleteLabAchievement=LabAcheivementService::deleteLabAchievement($lab_id);
            if(!$deleteLabAchievement){
                return false;
            }
            $labExternalLinks=LabExternalLinksService::deleteLabExternalLinks($lab_id);
            if(!$labExternalLinks){
                return false;
            }
            $labTagGroups=LabTagsGroupsService::deleteLabTagsGroups($lab_id);
            
            if(!$labTagGroups){
                return false;
            }
            $labSkillsGroupsService=LabSkillsGroupsStackService::deleteLabSkillsGroupsStack($lab_id);
            if(!$labSkillsGroupsService){
                return false;
            }
            $labAddress=LabAddressService::deleteLabAddress($lab_id);
           
            if(!$labAddress){
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
