<?php

namespace App\Services\Maestro\SocialLink;

use App\Models\SocialLink;
use Exception;

class SocialLinkService
{
    public static function getSocialLinkList()
    {
        try {
            return SocialLink::latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createSocialLink($request)
    {
        try {
            $socialLinkImage = null;
            if ($request->file('icon')) {
                $socialLinkImage = $request->file('icon')->store('uploads/social_link', 's3');
            }

            return SocialLink::create(['title' => $request->title, 'link' => $request->link, 'icon' => $socialLinkImage]);
        } catch (Exception $e) {
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
            return false;
        }
    }

    public static function getSocialLinkStatus()
    {
        try {
            return ['1' => 'Active', '0' => 'Not Active'];
        } catch (Exception $e) {
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
            return false;
        }
    }

    public static function updateSocialLinkById($id, $request)
    {
        try {
            $socialLink = SocialLink::findOrFail($id);
            if (!empty($socialLink)) {
                if ($request->file('icon')) {
                    $socialLink->icon = $request->file('icon')->store('uploads/social_link', 's3');
                }
                $socialLink->title = $request->title;
                if ($socialLink->save()) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
