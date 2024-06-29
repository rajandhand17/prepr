<?php

namespace App\Repositories\Api\Manage\CampusConnect;

use App\Helpers\CampusConnectHelper;
use App\Helpers\UtilityHelper;
use Exception;

class CampusConnectRepository implements CampusConnectInterface
{
    public function listSchools()
    {
        try {
            $schools = CampusConnectHelper::listSchools();

            return array_map(function ($item) {
                return [
                    'id'    => $item,
                    'title' => $item,
                ];
            }, $schools);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
