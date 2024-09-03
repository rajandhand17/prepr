<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollectionRating;
use App\Models\ResourceCollectionSocialActivity;

class ResourceCollectionSocialActivitiesService
{
    public function checkSocialActivity($resource_collection_id, $column, $action)
    {
        try {
            $checkActivity = ResourceCollectionSocialActivity::where(
                [
                    'resource_collection_id'  => $resource_collection_id,
                    'user_id'                 => auth()->user()->id,
                    $column                   => $action,
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

    public function captureSocialActivity($resource_collection_id, $column, $action): bool
    {
        try {
            ResourceCollectionSocialActivity::updateOrInsert([
                'user_id'               => auth()->user()->id,
                'resource_collection_id'=> $resource_collection_id,
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

    public static function getResourceCollectionBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $resource_collection_ids = ResourceCollectionSocialActivity::where(
                        [
                            'user_id'              => auth()->user()->id,
                            $columnValue['column'] => $columnValue['action'],
                        ]
                    )->get();

                    return $resource_collection_ids;
                }

                return false;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addRating($resource_collection_id, $request)
    {
        try {
            ResourceCollectionRating::updateOrInsert([
                'resource_collection_id'=> $resource_collection_id,
                'user_id'               => auth()->user()->id,
            ], [
                'rating' => $request->rating,
            ]);

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
