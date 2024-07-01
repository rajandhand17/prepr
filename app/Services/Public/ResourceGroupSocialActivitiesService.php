<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceGroupSocialActivity;

class ResourceGroupSocialActivitiesService
{
    public function checkSocialActivity($resource_group_id, $column, $action)
    {
        try {
            $checkActivity = ResourceGroupSocialActivity::where(
                [
                    'resource_group_id'   => $resource_group_id,
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

    public function captureSocialActivity($resource_group_id, $column, $action): bool
    {
        try {
            ResourceGroupSocialActivity::updateOrInsert([
                'user_id'           => auth()->user()->id,
                'resource_group_id' => $resource_group_id,
            ], [
                $column => $action,
            ]);

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceGroupsBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $organization_ids = ResourceGroupSocialActivity::where(
                        [
                            'user_id'              => auth()->user()->id,
                            $columnValue['column'] => $columnValue['action'],
                        ]
                    )->get();

                    return $organization_ids;
                }

                return false;
            }

            return false;
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

    public static function getResourceGroupBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $resource_group_ids = ResourceGroupSocialActivity::where(
                        [
                            'user_id'              => auth()->user()->id,
                            $columnValue['column'] => $columnValue['action'],
                        ]
                    )->get();

                    return $resource_group_ids;
                }

                return false;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
