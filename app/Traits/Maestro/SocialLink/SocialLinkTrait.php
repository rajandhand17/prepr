<?php

namespace App\Traits\Maestro\SocialLink;

use App\Services\Maestro\SocialLinkService;
use Exception;

trait SocialLinkTrait
{
    private function getSocialLinkList()
    {
        try {
            $socialMediaLinks = SocialLinkService::getSocialLinkList();
            if ($socialMediaLinks) {
                return $socialMediaLinks;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function createSocialLink($request)
    {
        try {
            if (SocialLinkService::createSocialLink($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteSocialLinkById($id)
    {
        try {
            if (SocialLinkService::deleteSocialLink($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getSocialLinkById($id)
    {
        try {
            return SocialLinkService::getSocialLinkById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateSocialLinkById($id, $request)
    {
        try {
            if (SocialLinkService::updateSocialLinkById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
