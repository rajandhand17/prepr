<?php

namespace App\Repositories\Api\Manage\UnifiedConnection;

use App\Models\User;

interface UnifiedConnectionInterface
{
    public function getIntegrations($data, User $user);

    public function listEmployee($connectionId, $stateData);

    public function inviteMembers($data);
}
