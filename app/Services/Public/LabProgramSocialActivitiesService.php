<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\LabProgramSocialActivity;
use App\Models\LabSocialActivity;
use Illuminate\Support\Facades\Auth;

class LabProgramSocialActivitiesService
{
    public function checkSocialActivity($labProgram, $column, $action)
    {
        try {
            $checkActivity = LabProgramSocialActivity::where(
                [
                    'lab_program_id'  => $labProgram,
                    'user_id'         => auth()->user()->id,
                    $column           => $action,
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

    public function captureSocialActivity($labProgram, $column, $action): bool
    {
        try {
            LabProgramSocialActivity::updateOrInsert([
                'user_id'         => Auth::user()->id,
                'lab_program_id'  => $labProgram,
            ], [
                $column => $action,
            ]);

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getLabProgramsBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $organization_ids = LabProgramSocialActivity::where(
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

    public static function getLabProgramBasedOnActivity($action)
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
            UtilityHelper::logError($e);
            return false;
        }
    }
}
