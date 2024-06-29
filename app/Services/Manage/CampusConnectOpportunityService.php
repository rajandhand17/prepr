<?php

namespace App\Services\Manage;

use App\Helpers\CampusConnectHelper;
use App\Helpers\UtilityHelper;
use App\Models\CampusConnectOpportunity;
use Exception;

class CampusConnectOpportunityService
{
    public static function updateOrCreate($id, $slug, $model, $data, $organization, $user, $skillIds)
    {
        try {
            $formattedData = CampusConnectHelper::prepareOpportunityData($id, $skillIds, $slug, $model, $data, $user, $organization);
            $serializedData = CampusConnectHelper::serializeOpportunityData($formattedData);
            $updateOrCreate = CampusConnectHelper::updateOrCreateOpportunity($serializedData);
            if ($updateOrCreate === false) {
                return false;
            }

            return CampusConnectOpportunity::query()->updateOrCreate([
                'model_id'   => $id,
                'model_type' => $model,
            ], [
                'ep_id'    => data_get($formattedData, 'id'),
                'language' => app()->getLocale(),
                'metadata' => $formattedData,
            ]);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public static function findByEpId($id)
    {
        try {
            return CampusConnectOpportunity::query()->where(
                ['ep_id' => $id]
            )->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public static function findByLargestEpId()
    {
        try {
            return CampusConnectOpportunity::query()->orderBy('ep_id', 'DESC')->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public static function findByModelTypeAndId($model, $id)
    {
        try {
            return CampusConnectOpportunity::query()->where(
                ['model_type' => $model, 'model_id' => $id]
            )->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
