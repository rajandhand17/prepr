<?php

namespace App\Listeners\Organization;

use App\Events\Organization\DeleteOrganizationAssociatedData;
use App\Services\Manage\OrganizationAddressService;
use App\Services\Manage\OrganizationMemberService;

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
            $organizationAddress = OrganizationAddressService::deleteOrganizationAddress($organizationId);
            if (!$organizationAddress) {
                return false;
            }
            $organizationMembers = OrganizationMemberService::deleteOrganizationMembers($organizationId);
            if (!$organizationMembers) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
