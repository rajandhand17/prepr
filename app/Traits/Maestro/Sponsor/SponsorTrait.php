<?php

namespace App\Traits\Maestro\Sponsor;

use App\Services\Maestro\Sponsor\SponsorService;
use Exception;

trait SponsorTrait
{
    private function getSponsorList()
    {
        try {
            $sponsorList = SponsorService::getSponsorList();
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
            if(SponsorService::createSponsor($request)){
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
            if(SponsorService::deleteSponsor($id)){
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
            return SponsorService::getSponsorById($id);
        } catch (Exception $e) {
            return false;
        }
    }
    private function updateSponsorById($id,$request)
    {
        try {
            if(SponsorService::updateSponsorById($id,$request)){
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
            $status = SponsorService::getSponsorStatus();
            if($status){
                return $status;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
