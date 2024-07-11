<?php

namespace App\Services\Maestro;

use App\Models\LabExternalLinks;

class LabExternalLinksService
{
    public static function createLabExternalLinks($lab, $newLab)
    {
        try {
            $labExternalLink = LabExternalLinks::where('lab_id', $lab->id)->get();
            if (count($labExternalLink) > 0) {
                foreach ($labExternalLink as $links) {
                    $labExternalLink = new LabExternalLinks();
                    $labExternalLink->lab_id = $newLab->id;
                    $labExternalLink->social_media_link = $links->social_media_link;
                    $labExternalLink->social_link_id = $links->social_link_id;
                    $labExternalLink->save();
                }
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
