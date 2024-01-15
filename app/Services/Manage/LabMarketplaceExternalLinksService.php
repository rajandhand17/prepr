<?php

namespace App\Services\Manage;

use App\Models\LabExternalLinks;
use App\Models\LabMarketplaceExternalLink;
use App\Models\LabTemplateExternalLink;

class LabMarketplaceExternalLinksService
{
    public function createLabMarketplaceExternalLinks($labMarketplaceId, $labId)
    {
        try {
            $existsLabExternalLink = LabExternalLinks::where('lab_id', $labId)->get();
            if ($existsLabExternalLink) {
                foreach ($existsLabExternalLink as $externalLinks) {
                    $labMarketplaceExternalLink = new LabMarketplaceExternalLink();
                    $labMarketplaceExternalLink->lab_marketplace_id = $labMarketplaceId;
                    $labMarketplaceExternalLink->social_media_link = $externalLinks->external_links;
                    $labMarketplaceExternalLink->social_link_id = $externalLinks->social_link_id;
                    $labMarketplaceExternalLink->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteLabMarketplaceExternalLink($labMarketplaceId){
        try {
            $labMarketplaceExternalLink=LabMarketplaceExternalLink::where('lab_marketplace_id',$labMarketplaceId)->first();
            if($labMarketplaceExternalLink){
                $labMarketplaceExternalLink=LabMarketplaceExternalLink::where('lab_marketplace_id',$labMarketplaceId)->first();
                if(!$labMarketplaceExternalLink){
                    return false;
                }
            }
            return true;
        }catch (\Exception $e) {
            return false;
        }
    }
}
