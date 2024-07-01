<?php

namespace App\Services\Maestro\CommunityTrophy;

use App\Models\CommunityTrophy;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CommunityTrophyService
{
    public static function updateCommunityTrophyById($id, $request)
    {
        try {
            $trophy = communityTrophy::find($id);
            $input = $request->all();
            $roles = $request->roles;
            $validation_array = [
                'name'       => 'required|max:25',
                'image'      => 'mimes:jpg,png,jpeg',
                'points'     => 'required|integer|min:0',
                'badge_type' => 'required',
                'criteria'   => 'required|max:100',
            ];
            $image = '';
            if ($request->image) {
                $image = $request->image->store('uploads/trophy', 's3');
            }
            $validation = Validator::make($request->all(), $validation_array);
            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation)->withInput();
            }
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('community_trophy', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            if ($image !== '') {
                $insertArray['image'] = $image;
            }
            if (!empty($insertArray)) {
                communityTrophy::where('id', $id)->update($insertArray);

                //BadgeDetail::where('award_id', $id)->where('award_type', 'community')->delete();

                $badgeData = [
                    'issuer'     => \Auth::user()->id,
                    'criteria'   => $request->criteria,
                    'award_id'   => $id,
                    'badge'      => $request->badge_type,
                    'award_type' => 'community',
                ];

                // BadgeDetail::create($badgeData);
                return redirect()->route('communitytrophy.index')->with('success', 'Community Medal updated successfully');
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteCommunityTrophy($id)
    {
        try {
            $CommunityTrophy = CommunityTrophy::find($id);

            if ($CommunityTrophy) {
                return $CommunityTrophy->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createCommunityTrophy($request)
    {
        try {
            $input = $request->all();
            $validation_array = [
                'name'       => 'required|max:25',
                'image'      => 'required|mimes:jpg,png,jpeg',
                'points'     => 'required|integer|min:0',
                'badge_type' => 'required',
                'criteria'   => 'required|max:100',
            ];

            $image = '';
            if ($request->image) {
                $image = $request->image->store('uploads/trophy', 's3');
            }
            $validation = Validator::make($request->all(), $validation_array);
            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation)->withInput();
            }
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('community_trophy', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            $insertArray['image'] = $image;

            if ($insertArray !== null) {
                $trophy = CommunityTrophy::create($insertArray);

                $badgeData = [
                    'issuer'     => \Auth::user()->id,
                    'criteria'   => $request->criteria,
                    'award_id'   => $trophy->id,
                    'badge'      => $request->badge_type,
                    'award_type' => 'community',
                ];

                //BadgeDetail::create($badgeData);
                return redirect()->route('communitytrophy.index')->with('success', 'Community Medal has created successfully');
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCommunityTrophy()
    {
        try {
            return CommunityTrophy::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
