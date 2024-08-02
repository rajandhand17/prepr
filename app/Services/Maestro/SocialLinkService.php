<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Helpers\UtilityHelper;
use App\Models\SocialLink;
use Exception;

class SocialLinkService
{
    public static function getSocialLinkList()
    {
        try {
            return SocialLink::latest();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createSocialLink($request)
    {
        try {
            $socialLinkImage = null;
            if ($request->file('icon')) {
                $socialLinkImage = FileUploadHelper::uploadImageToS3($request->file('icon'), 'social_link_icon');
            }

            return SocialLink::create(['title' => $request->title, 'icon' => $socialLinkImage]);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteSocialLink($id)
    {
        try {
            $socialLink = SocialLink::find($id);
            if (!empty($socialLink)) {
                return $socialLink->delete();
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getSocialLinkById($id)
    {
        try {
            $socialLink = SocialLink::findOrFail($id);
            if ($socialLink != null) {
                return $socialLink;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateSocialLinkById($id, $request)
    {
        try {
            $socialLink = SocialLink::findOrFail($id);
            if (!empty($socialLink)) {
                if ($request->file('icon')) {
                    $socialLink->icon = FileUploadHelper::uploadImageToS3($request->file('icon'), 'social_link_icon');
                }
                $socialLink->title = $request->title;
                if ($socialLink->save()) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
