<?php

namespace App\Services\Public;

use App\Helpers\MixpanelHelper;
use App\Models\LabSocialActivity;
use Illuminate\Support\Facades\Auth;

class LabSocialActivitiesService
{
    public function checkSocialActivity($lab_id, $column, $action)
    {
        try {
            if (auth()->check()) {
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
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($lab_id, $column, $action): bool
    {
        try {
            if (auth()->check()) {
                LabSocialActivity::updateOrInsert([
                    'user_id' => auth::user()->id,
                    'lab_id'  => $lab_id,
                ], [
                    $column => $action,
                ]);
                if ($column = 'favourite') {
                    $fav_or_unfav = $action == 1 ? 'favourite' : 'un-favourite';
                    $fav_data = [
                        'fav_or_unfav' => $fav_or_unfav,
                        'fav_type'     => 'lab',
                    ];
                    MixpanelHelper::mixpanel_tracking(config('mixpanel.fav_or_unfav'), $fav_data, auth()->user(), request()->ip());
                }

                return true;
            }

            return false;
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
            return false;
        }
    }

    public static function getFeaturedLabIds()
    {
        try {
            $lab_ids = LabSocialActivity::select('lab_id')->where('user_id', '!=', auth()->user()->id)
                ->orderBy('like_dislike', 'ASC')
                ->orderBy('share', 'ASC')
                ->take(config('site-settings.explore_page_limit_min'))
                ->get();

            return $lab_ids;
        } catch(\Exception $e) {
            return false;
        }
    }
}
