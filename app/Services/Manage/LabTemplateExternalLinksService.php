<?php

namespace App\Services\Manage;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabExternalLinks;
use App\Models\LabTemplate;
use App\Models\LabTemplateExternalLink;
use HiFolks\RandoPhp\Randomize;

class LabTemplateExternalLinksService
{
    public function createLabTemplateExternalLinks($labTemplateId, $lab)
    {
        try {
            $existsLabExternalLink = LabExternalLinks::where('lab_id', $lab->id)->get();
            if ($existsLabExternalLink) {
                foreach ($existsLabExternalLink as $externalLinks) {
                    $labTemplateExternalLink = new LabTemplateExternalLink();
                    $labTemplateExternalLink->template_lab_id = $labTemplateId->id;
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
