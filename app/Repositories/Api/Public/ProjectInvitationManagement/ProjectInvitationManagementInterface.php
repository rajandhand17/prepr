<?php

namespace App\Repositories\Api\Public\ProjectInvitationManagement;

interface ProjectInvitationManagementInterface
{
    public function checkJoinUnjoinStatus($request, $checkComponentBasedOnSlug);

    public function acceptOrRejectJoinRequest($request, $checkComponentBasedOnSlug, $action);
}
