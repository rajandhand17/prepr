<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleSocialActivities;

class ResourceModuleSocialActivitiesService
{
    public function checkSocialActivity($resource_module_id, $column, $action)
    {
        try {
            $checkActivity = ResourceModuleSocialActivities::where(
                [
                    'resource_module_id'  => $resource_module_id,
                    'user_id'             => auth()->user()->id,
                    $column               => $action,
                ]
            )->first();
            if ($checkActivity != null) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function captureSocialActivity($resource_module_id, $column, $action): bool
    {
        try {
            ResourceModuleSocialActivities::updateOrInsert([
                'user_id'           => auth()->user()->id,
                'resource_module_id'=> $resource_module_id,
            ], [
                $column => $action,
            ]);

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getColumnNameValue($action)
    {
        try {
            $column = null;
            $value = null;
            switch ($action) {
                case 'like':
                    $column = 'like_dislike';
                    $value = '1';
                    break;
                case 'un-like':
                    $column = 'like_dislike';
                    $value = '2';
                    break;
                case 'share':
                    $column = 'share';
                    $value = '1';
                    break;
                case 'favourite':
                    $column = 'favourite';
                    $value = '1';
                    break;
                case 'un-favourite':
                    $column = 'favourite';
                    $value = '2';
                    break;
                default:
                    $column = null;
                    $value = null;
                    break;
            }
            if ($column != null && $value != null) {
                return ['column' => $column, 'action' => $value];
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $resource_module_ids = ResourceModuleSocialActivities::where(
                        [
                            'user_id'              => auth()->user()->id,
                            $columnValue['column'] => $columnValue['action'],
                        ]
                    )->get();

                    return $resource_module_ids;
                }

                return false;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function resourceModuleFavouriteIds($userData)
    {
        try {
            $resourceModuleFavouriteIds = ResourceModuleSocialActivities::where(['user_id' => $userData->id, 'favourite' => '1'])->pluck('resource_module_id');

            return $resourceModuleFavouriteIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
