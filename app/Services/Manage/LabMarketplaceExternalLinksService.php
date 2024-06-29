<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabExternalLinks;
use App\Models\LabMarketplaceExternalLink;
use Exception;

class LabMarketplaceExternalLinksService
{
    public function addLabMarketplaceExternalLinks($labMarketplaceId, $labId)
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

    public function redeemLabMarketplaceExternalLinks($redeemLabId, $labMarketplaceId)
    {
        try {
            $labMarketplaceExternalLinkDatas = LabMarketplaceExternalLink::where('lab_marketplace_id', $labMarketplaceId)->get();
            if (!empty($labMarketplaceExternalLinkDatas)) {
                foreach ($labMarketplaceExternalLinkDatas as $labMarketplaceExternalLink) {
                    $newLabExternalLink = new LabExternalLinks();
                    $newLabExternalLink->lab_id = $redeemLabId;
                    $newLabExternalLink->social_media_link = $labMarketplaceExternalLink->social_media_link;
                    $newLabExternalLink->social_link_id = $labMarketplaceExternalLink->social_link_id;
                    $newLabExternalLink->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteLabMarketplaceExternalLink($labMarketplaceId)
    {
        try {
            $labMarketplaceExternalLink = LabMarketplaceExternalLink::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($labMarketplaceExternalLink) {
                $labMarketplaceExternalLink = LabMarketplaceExternalLink::where('lab_marketplace_id', $labMarketplaceId)->first();
                if (!$labMarketplaceExternalLink) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
