<?php

namespace App\Repositories\Api\Public\InvitationManagement;

interface InvitationManagementInterface
{
    public function checkComponentJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);

    public function acceptOrRejectComponentJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
}
