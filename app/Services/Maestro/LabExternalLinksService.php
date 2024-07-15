<?php

namespace App\Services\Maestro;

use App\Models\LabExternalLinks;

class LabExternalLinksService
{
    public static function createLabExternalLinks($originalLabsTags, $clonedLabId)
    {
        try {
            $originalLabsTags->each(function ($external_links) use ($clonedLabId) {
                if ($external_links) {
                    $cloneExternalLink = $external_links->replicate();
                    $cloneExternalLink->lab_id = $clonedLabId;
                    $cloneExternalLink->save();
                }
            });
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
