<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabExternalLinks;
use App\Models\LabMarketplaceExternalLink;
use Exception;

class LabMarketplaceExternalLinksService
{
    public static function addLabMarketplaceExternalLinks($labMarketplaceId, $labId)
    {
        try {
            $existsLabExternalLink = LabExternalLinks::where('lab_id', $labId)->get();
            if ($existsLabExternalLink) {
                foreach ($existsLabExternalLink as $externalLinks) {
                    $labMarketplaceExternalLink = new LabMarketplaceExternalLink();
                    $labMarketplaceExternalLink->lab_marketplace_id = $labMarketplaceId;
                    $labMarketplaceExternalLink->social_media_link = $externalLinks->social_media_link;
                    $labMarketplaceExternalLink->social_link_id = $externalLinks->social_link_id;
                    $labMarketplaceExternalLink->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
