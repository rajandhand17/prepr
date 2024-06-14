<?php

namespace App\Traits\Maestro\SocialLink;

use App\Services\Maestro\SocialLink\SocialLinkService;
use Exception;

trait SocialLinkTrait
{
    private function getSocialLinkList()
    {
        try {
            $sponsorList = SocialLinkService::getSocialLinkList();
            if($sponsorList){
                return $sponsorList;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function createSocialLink($request)
    {
        try {
            if(SocialLinkService::createSocialLink($request)){
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
            if(SocialLinkService::deleteSocialLink($id)){
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
    private function updateSocialLinkById($id,$request)
    {
        try {
            if(SocialLinkService::updateSocialLinkById($id,$request)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
