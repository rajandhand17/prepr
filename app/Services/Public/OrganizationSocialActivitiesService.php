<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationSocialActivities;

class OrganizationSocialActivitiesService
{
    public static function getColumnNameValue($action)
    {
        try {
            $column = null;
            $value = null;
            switch ($action) {
                case 'follow':
                    $column = 'follow_unfollow';
                    $value = '1';
                    break;
                case 'un-follow':
                    $column = 'follow_unfollow';
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
                case 'like':
                    $column = 'like_dislike';
                    $value = '1';
                    break;
                case 'un-like':
                    $column = 'like_dislike';
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

    public function checkSocialActivity($organization_id, $column, $action)
    {
        try {
            $checkActivity = OrganizationSocialActivities::where(
                [
                    'organization_id' => $organization_id,
                    'user_id'         => auth()->user()->id,
                    $column           => $action,
                ]
            )->first();
            if ($checkActivity != null) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function captureSocialActivity($id, $column, $action)
    {
        try {
            OrganizationSocialActivities::updateOrInsert([
                'user_id'         => auth()->user()->id,
                'organization_id' => $id,
            ], [
                $column => $action,
            ]);

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getOrganizationsBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $organization_ids = OrganizationSocialActivities::where(
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
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
