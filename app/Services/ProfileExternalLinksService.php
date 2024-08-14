<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ProfileExternalLinks;

class ProfileExternalLinksService
{

    public static function updateProfileExternalLinks($request, $user_id)
    {
        try {
            if ($request->has('external_links') && $request->get('external_link_ids')) {
                if (count($request->external_link_ids) > 0) {
                    $existExternalLinks = ProfileExternalLinks::where('user_id', $user_id)->pluck('social_link_id')->toArray();
                    $nonExistingIds = array_diff($existExternalLinks, $request->external_link_ids);
                    $deleteNonExistingExternalLinks = ProfileExternalLinks::where('user_id', $user_id)->whereIn('social_link_id', $nonExistingIds)->delete();
                    foreach ($request->external_link_ids as $key => $value) {
                        $userExternalLink = ProfileExternalLinks::select('id', 'social_media_link')->where([
                            ['user_id', '=', $user_id],
                            ['social_link_id', '=', $value],
                        ])->first();
                        if ($userExternalLink) {
                            if ($userExternalLink['social_media_link'] !== $request->external_links[$key]) {
                                $userExternalLink->social_media_link = $request->external_links[$key];
                                $userExternalLink->save();
                            }
                        }
                        if (!$userExternalLink) {
                            if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                                $userExternalLink = new ProfileExternalLinks();
                                $userExternalLink->user_id = $user_id;
                                $userExternalLink->social_media_link = $request->external_links[$key];
                                $userExternalLink->social_link_id = $value;
                                $userExternalLink->save();
                            }
                        }
                    }
                } 
            } else {
            self::deleteProfileExternalLinks($user_id);                
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
    public static function deleteProfileExternalLinks($user_id)
    {
        try {
            // Fetch the IDs of the profile external links
            $checkExists = ProfileExternalLinks::where('user_id', $user_id)->pluck('id');
            // Check if any IDs exist
            if (!$checkExists->isEmpty()) {
                // Delete the records that match the retrieved IDs
                $deleteProfileExternalLinks = ProfileExternalLinks::whereIn('id', $checkExists)->delete();
                // If deletion failed, return false
                if (!$deleteProfileExternalLinks) {
                    return false;
                }
            }
    
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
    
            return false;
        }
    }
}