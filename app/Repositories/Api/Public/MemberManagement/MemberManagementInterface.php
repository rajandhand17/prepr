<?php

namespace App\Repositories\Api\Public\MemberManagement;

interface MemberManagementInterface
{
    public function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
}
