<?php

namespace App\Repositories\Api\Manage\CampusConnect;

use App\Helpers\CampusConnectHelper;
use Exception;

class CampusConnectRepository implements CampusConnectInterface
{
    public function listSchools()
    {
        try {
            return CampusConnectHelper::listSchools();
        } catch (Exception $exception) {
            return false;
        }
    }
}
