<?php

namespace App\Services\Manage;

use App\Helpers\CampusConnectHelper;
use App\Helpers\UtilityHelper;
use App\Models\CampusConnectStory;
use Exception;

class CampusConnectStoryService
{
    public static function UpdateOrCreate($id, $slug, $model, $data, $organization)
    {
        try {
            $formattedData = CampusConnectHelper::prepareStoryData($id, $slug, $model, $data, $organization);
            $serializedData = CampusConnectHelper::serializeStoryData($formattedData);
            $updateOrCreate = CampusConnectHelper::updateOrCreateStory($serializedData);
            if ($updateOrCreate === false) {
                return false;
            }
            $campusContent = CampusConnectStory::query()->updateOrCreate([
                'model_id'   => $id,
                'model_type' => $model,
            ], [
                'ep_id'    => data_get($formattedData, 'id'),
                'language' => app()->getLocale(),
                'metadata' => $formattedData,
            ]);

            return $campusContent;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function findByEpId($id)
    {
        try {
            return CampusConnectStory::query()->where(
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
            return CampusConnectStory::query()->orderBy('ep_id', 'DESC')->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function findByModelTypeAndId($model, $id)
    {
        try {
            return CampusConnectStory::query()->where(
                ['model_type' => $model, 'model_id' => $id]
            )->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
