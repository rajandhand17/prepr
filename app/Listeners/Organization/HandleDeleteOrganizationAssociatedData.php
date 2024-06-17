<?php

namespace App\Listeners\Organization;

use App\Events\Organization\DeleteOrganizationAssociatedData;
use App\Helpers\ChargebeeHelper;
use App\Services\Manage\ChallengePathService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeTemplateService;
use App\Services\Manage\LabMarketplaceService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationAddressService;
use App\Services\Manage\OrganizationCustomizationService;
use App\Services\Manage\OrganizationMemberService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;

class HandleDeleteOrganizationAssociatedData
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
    public function handle(DeleteOrganizationAssociatedData $event)
    {
        try {
            $organizationId = $event->organizationId;
            $organizationSubscription = ChargebeeHelper::deleteOrganizationSubscription($organizationId);
            if (!$organizationSubscription) {
                return false;
            }
            $organizationAddress = OrganizationAddressService::deleteOrganizationAddress($organizationId);
            if (!$organizationAddress) {
                return false;
            }
            $organizationMembers = OrganizationMemberService::deleteOrganizationMembers($organizationId);
            if (!$organizationMembers) {
                return false;
            }
            $organizationCustomization = OrganizationCustomizationService::organizationCustomization($organizationId);
            if (!$organizationCustomization) {
                return false;
            }
            $organizationLab = LabService::deleteOrganizationLab($organizationId);
            if (!$organizationLab) {
                return false;
            }
            $organizationLabProgram = LabProgramService::deleteOrganizationLabProgram($organizationId);
            if (!$organizationLabProgram) {
                return false;
            }
            $organizationLabMarketPlace = LabMarketplaceService::deleteOrganizationLabMarketPlace($organizationId);
            if (!$organizationLabMarketPlace) {
                return false;
            }
            $organizationChallenge = ChallengeService::deleteOrganizationChallenge($organizationId);
            if (!$organizationChallenge) {
                return false;
            }
            $organizationChallengePath = ChallengePathService::deleteOrganizationChallengePath($organizationId);
            if (!$organizationChallengePath) {
                return false;
            }
            $organizationChallengeTemplate = ChallengeTemplateService::deleteOrganizationChallengeTemplate($organizationId);
            if (!$organizationChallengeTemplate) {
                return false;
            }
            $organizationResourceModule = ResourceModuleService::deleteOrganizationResourceModule($organizationId);
            if (!$organizationResourceModule) {
                return false;
            }
            $organizationResourceCollection = ResourceCollectionService::deleteOrganizationResourceCollection($organizationId);
            if (!$organizationResourceCollection) {
                return false;
            }
            $organizationResourceModule = ResourceGroupService::deleteOrganizationResourceGroup($organizationId);
            if (!$organizationResourceModule) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
