<?php

namespace App\Repositories\Api\Public\InvitationManagement;

interface InvitationManagementInterface
{
    public function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
}
