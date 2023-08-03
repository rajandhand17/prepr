<?php

namespace App\Services\Public;

use App\Models\LabSocialActivity;
use Illuminate\Support\Facades\Auth;

class LabSocialActivitiesService
{
    public function checkLabActivity($id, $column, $value)
    {
        return LabSocialActivity::where([
            'user_id' => Auth::user()->id,
            'lab_id'  => $id,
            $column   => $value,
        ])->first();
    }

    public function store($id, $column, $action)
    {
        try {
            $uniqueKey = ['user_id' => Auth::user()->id,
                'lab_id'            => $id,
            ];
            $productData = [
                $column => $action,
            ];
            $records = LabSocialActivity::updateOrInsert($uniqueKey, $productData);
            if ($records) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function joinLab($lab_id)
    {
        try {
            $checkJoinOrNot = LabSocialActivity::where([
                ['user_id', '=', Auth::user()->id],
                ['lab_id', '=', $lab_id],
            ])->first();
            if (!$checkJoinOrNot) {
                $follow = new LabSocialActivity();
                $follow->user_id = Auth::user()->id;
                $follow->lab_id = $lab_id;
                $follow->join_unjoin = '1';
                if ($follow->save()) {
                    return true;
                }
            }
            $checkJoinOrNot->join_unjoin = '1';
            if ($checkJoinOrNot->save()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unjoinLab($lab_id)
    {
        try {
            $checkJoinOrNot = LabSocialActivity::where([
                ['user_id', '=', Auth::user()->id],
                ['lab_id', '=', $lab_id],
            ])->first();
            if (!$checkJoinOrNot) {
                $follow = new LabSocialActivity();
                $follow->user_id = Auth::user()->id;
                $follow->lab_id = $lab_id;
                $follow->join_unjoin = '2';
                if ($follow->save()) {
                    return true;
                }
            }
            $checkJoinOrNot->join_unjoin = '2';
            if ($checkJoinOrNot->save()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function followLab($lab_id)
    {
        try {
            $checkFollow = LabSocialActivity::where([
                ['user_id', '=', Auth::user()->id],
                ['lab_id', '=', $lab_id],
            ])->first();
            if (!$checkFollow) {
                $follow = new LabSocialActivity();
                $follow->user_id = Auth::user()->id;
                $follow->lab_id = $lab_id;
                $follow->follow_unfollow = '1';
                if ($follow->save()) {
                    return true;
                }
            }
            $checkFollow->follow_unfollow = '1';
            if ($checkFollow->save()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function unfollowLab($lab_id)
    {
        try {
            $checkFollow = LabSocialActivity::where([
                ['user_id', '=', Auth::user()->id],
                ['lab_id', '=', $lab_id],
            ])->first();
            if (!$checkFollow) {
                $follow = new LabSocialActivity();
                $follow->user_id = Auth::user()->id;
                $follow->lab_id = $lab_id;
                $follow->follow_unfollow = '2';
                if ($follow->save()) {
                    return true;
                }
            }
            $checkFollow->follow_unfollow = '2';
            if ($checkFollow->save()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function share($lab_id)
    {
        try {
            $share = LabSocialActivity::where([
                ['user_id', '=', Auth::user()->id],
                ['lab_id', '=', $lab_id],
            ])->first();
            if (!$share) {
                $share = new LabSocialActivity();
                $share->user_id = Auth::user()->id;
                $share->lab_id = $lab_id;
                $share->share = '1';
                if ($share->save()) {
                    return true;
                }

                return false;
            }
            $share->share = '1';
            if ($share->save()) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
}
