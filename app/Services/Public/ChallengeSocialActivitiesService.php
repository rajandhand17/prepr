<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeSocialActivity;
use Illuminate\Support\Facades\Auth;

class ChallengeSocialActivitiesService
{
    public function checkSocialActivity($challenge_id, $column, $action)
    {
        try {
            if (auth()->check()) {
                $checkActivity = ChallengeSocialActivity::where(
                    [
                        'challenge_id'  => $challenge_id,
                        'user_id'       => auth()->user()->id,
                        $column         => $action,
                    ]
                )->first();
                if ($checkActivity != null) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function captureSocialActivity($challenge_id, $column, $action): bool
    {
        try {
            if (auth()->check()) {
                ChallengeSocialActivity::updateOrInsert([
                    'user_id'       => auth::user()->id,
                    'challenge_id'  => $challenge_id,
                ], [
                    $column => $action,
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeBasedOnActivity($action)
    {
        try {
            if (auth()->check()) {
                $columnValue = self::getColumnNameValue($action);
                if ($columnValue !== false) {
                    $organization_ids = ChallengeSocialActivity::where(
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
}
