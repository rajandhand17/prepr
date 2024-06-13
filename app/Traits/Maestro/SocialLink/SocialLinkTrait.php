<?php

namespace App\Traits\Maestro\SocialLink;

use App\Services\Maestro\SocialLink\SocialLinkService;
use Exception;

trait SocialLinkTrait
{
    private function getSponsorList()
    {
        try {
            $sponsorList = SocialLinkService::getSponsorList();
            if($sponsorList){
                return $sponsorList;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function createSponsor($request)
    {
        try {
            if(SocialLinkService::createSponsor($request)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function deleteSponsorById($id)
    {
        try {
            if(SocialLinkService::deleteSponsor($id)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function getSponsorById($id)
    {
        try {
            return SocialLinkService::getSponsorById($id);
        } catch (Exception $e) {
            return false;
        }
    }
    private function updateSponsorById($id,$request)
    {
        try {
            if(SocialLinkService::updateSponsorById($id,$request)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function getSponsorStatus()
    {
        try {
            $status = SocialLinkService::getSponsorStatus();
            if($status){
                return $status;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
