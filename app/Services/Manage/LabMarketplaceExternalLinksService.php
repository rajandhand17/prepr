<?php

namespace App\Services\Manage;

use App\Models\LabExternalLinks;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTemplateExternalLink;
use App\Models\LabTemplateSkillsGroupsStack;

class LabMarketplaceExternalLinksService
{
    public function createLabMarketplaceExternalLinks($labMarketplaceId, $lab)
    {
        try {
            $existsLabExternalLink = LabExternalLinks::where('lab_id', $lab->id)->get();
            if ($existsLabExternalLink) {
                foreach ($existsLabExternalLink as $externalLinks) {
                    $labTemplateExternalLink = new LabTemplateExternalLink();
                    $labTemplateExternalLink->template_lab_id = $labMarketplaceId->id;
                    $labTemplateExternalLink->social_media_link = $externalLinks->external_links;
                    $labTemplateExternalLink->social_link_id = $externalLinks->social_link_id;
                    $labTemplateExternalLink->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
