<?php

namespace App\Services\Maestro;

use App\Models\TrophyAwards;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class TrophyAwardsService
{
    public static function updateTrophyAwardsById($id, $request)
    {
        try {
            $trophyAward = TrophyAwards::find($id);
            $input = $request->all();
            $validation_array = [
                'name'                  => 'required|max:25',
                'criteria'              => 'required|max:100',
                'issue_trophy_date'     => 'required',
                'expiration_date'       => 'required',
                'trophy_code_id'        => 'required',
                'no_of_times_issued'    => 'required',
                'status'                => 'required',
                'description'           => 'required|max:500',
                'user_id'               => 'required',
                'points_gained'         => 'required|integer|min:0',
                'badge_type'            => 'required',
            ];
            $image = '';
            if ($request->hasFile('image')) {
                $image = $request->image->store('uploads/awardedTrophy', 's3');
            }

            $validation = Validator::make($request->all(), $validation_array);
            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation)->withInput();
            }

            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('trophy_awards', $key)) {
                    $insertArray[$key] = $value;
                }
                if ($image !== '') {
                    $insertArray['image'] = $image;
                }
                $insertArray['user_id'] = implode(',', $input['user_id']);
            }
            if (!empty($insertArray)) {
                $trophyData = TrophyAwards::where('id', $id)->update($insertArray);
                // BadgeDetail::where('award_id', $id)->where('award_type', 'trophy')->delete();

                $badgeData = [
                    'issuer'   => \Auth::user()->id,
                    'criteria' => $request->criteria,
                    'award_id' => $id,
                    'badge'    => $request->badge_type,
                ];

                // BadgeDetail::create($badgeData);

                // get old user ids
                $oldUserIds = explode(',', $trophyAward->user_id);
                // get new add user ids
                $newAddedUserIds = array_diff($input['user_id'], $oldUserIds);

                // send trophy code id to new update awarded user
                if ($input['trophy_code_id'] !== null) {
                    $trophy_data = [];
                    foreach ($newAddedUserIds as $userId) {
                        // get user data
                        $user = User::find($userId);
                        // get updated awarded trophy data
                        $trophyData = TrophyAwards::find($id);

                        if ($user !== null) {
                            // mail data
                            $data = ['mail_template' => 'send_awarded_trophy_code_to_user', 'name' => $user->name, 'trophyName' => $trophyData->name, 'trophyCodeID' => $trophyData->trophy_code_id, 'tropyPic' => $trophyData->image, 'email' => $user->email, 'to_email' => $user->email, 'to_name' => $user->username, 'fullname' => $user->name, 'title' => 'You won the Awarded Trophy '.$trophyData->name];
                            $trophy_data[] = $data;
                            if (!empty($user->email)) {
                                // send mail if user have set privacy subscribe
                                if ($user->is_subscribe == 'subscribe') {
                                    Event::dispatch('send-template', [$data]);
                                }
                            }
                        }
                    }
                    // if (!empty($trophy_data)) {
                    //     // Mixpanel tracking code: update trophy (only triggered if the userlist changes)
                    //     MixpanelHelper::mixpanel_tracking(config('mixpanel.update_sent_trophy'), $trophy_data, Auth::user(), $request->ip());
                    // }
                }

                return true;
            }
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function deleteTrophyAwards($id)
    {
        try {
            $TrophyAwards = TrophyAwards::find($id);

            if ($TrophyAwards) {
                return $TrophyAwards->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createTrophyAwards($request)
    {
        try {
            $input = $request->all();

            $validation_array = [
                'name'                  => 'required|max:25',
                'criteria'              => 'required|max:100',
                'issue_trophy_date'     => 'required',
                'expiration_date'       => 'required',
                'no_of_times_issued'    => 'required',
                'status'                => 'required',
                'description'           => 'required|max:500',
                'image'                 => 'required|mimes:jpg,png,jpeg',
                'user_id'               => 'required',
                'points_gained'         => 'required|integer|min:0',
                'badge_type'            => 'required',

            ];

            $image = '';
            if ($request->hasFile('image')) {
                $image = $request->image->store('uploads/awardedTrophy', 's3');
            }
            $validation = Validator::make($request->all(), $validation_array);
            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation)->withInput();
            }
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('trophy_awards', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            $insertArray['image'] = $image;
            $insertArray['user_id'] = implode(',', $input['user_id']);

            if (!empty($insertArray)) {
                $trophyData = TrophyAwards::create($insertArray);

                $badgeData = [
                    'issuer'   => Auth::user()->id,
                    'criteria' => $request->criteria,
                    'award_id' => $trophyData->id,
                    'badge'    => $request->badge_type,
                ];

                // BadgeDetail::create($badgeData);

                // send trophy code id to awarded user
                if ($input['trophy_code_id'] !== null) {
                    $trophy_data = [];
                    foreach ($input['user_id'] as $userId) {
                        // get user data
                        $user = User::find($userId);

                        if ($user !== null) {
                            // mail data
                            $data = ['mail_template' => 'send_awarded_trophy_code_to_user', 'name' => $user->name, 'trophyName' => $trophyData->name, 'trophyCodeID' => $trophyData->trophy_code_id, 'tropyPic' => $trophyData->image, 'email' => $user->email, 'to_email' => $user->email, 'to_name' => $user->username, 'fullname' => $user->name, 'title' => 'You won the Awarded Trophy '.$trophyData->name];
                            $trophy_data[] = $data;
                            if (!empty($user->email)) {
                                // send mail if user have set privacy subscribe
                                if ($user->is_subscribe == 'subscribe') {
                                    Event::dispatch('send-template', [$data]);
                                }
                            }
                        }
                    }
                    // if (!empty($trophy_data)) {
                    //     // Mixpanel tracking code: send trophy (via maestro)
                    //     MixpanelHelper::mixpanel_tracking(config('mixpanel.send_trophy'), $trophy_data, Auth::user(), $request->ip());
                    // }
                }

                return true;
            }
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function getTrophyAwards()
    {
        try {
            return TrophyAwards::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getTrophyAwardsById($id)
    {
        try {
            return TrophyAwards::find($id);
        } catch (Exception $e) {
            return false;
        }
    }
}
