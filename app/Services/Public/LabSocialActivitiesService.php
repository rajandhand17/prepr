<?php

namespace App\Services\Public;

use App\Models\LabSocialActivity;
use Illuminate\Support\Facades\Auth;

class LabSocialActivitiesService
{
    public function checkSocialActivity($lab_id, $column, $action)
    {
        try {
            $checkActivity = LabSocialActivity::where(
                [
                    'lab_id'  => $lab_id,
                    'user_id' => auth()->user()->id,
                    $column   => $action,
                ]
            )->first();
            if ($checkActivity != null) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($lab_id, $column, $action): bool
    {
        try {
            LabSocialActivity::updateOrInsert([
                'user_id' => Auth::user()->id,
                'lab_id'  => $lab_id,
            ], [
                $column => $action,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabsBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $organization_ids = LabSocialActivity::where(
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
                case 'dis-like':
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
            return false;
        }
    }

    public static function getLabBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $lab_ids = LabSocialActivity::where(
                        [
                            'user_id'              => auth()->user()->id,
                            $columnValue['column'] => $columnValue['action'],
                        ]
                    )->get();

                    return $lab_ids;
                }

                return false;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
}
