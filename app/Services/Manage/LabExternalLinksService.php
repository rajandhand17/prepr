<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabExternalLinks;

class LabExternalLinksService
{
    public function createLabExternalLinks($request, $lab)
    {
        if ($request->has('external_links') && $request->get('external_link_ids')) {
            if (count($request->external_link_ids) > 0) {
                foreach ($request->external_link_ids as $key => $value) {
                    if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                        $labExternalLink = new LabExternalLinks();
                        $labExternalLink->lab_id = $lab->id;
                        $labExternalLink->social_media_link = $request->external_links[$key];
                        $labExternalLink->social_link_id = $value;
                        $labExternalLink->save();
                    }
                }
            }
        }

        return true;
    }

    public function updateLabExternalLinks($request, $lab_id)
    {
        try {
            if ($request->has('external_links') && $request->get('external_link_ids')) {
                if (count($request->external_link_ids) > 0) {
                    $existExternalLinks = LabExternalLinks::where('lab_id', $lab_id)->pluck('social_link_id')->toArray();
                    $nonExistingIds = array_diff($existExternalLinks, $request->external_link_ids);
                    $deleteNonExistingExternalLinks = LabExternalLinks::where('lab_id', $lab_id)->whereIn('social_link_id', $nonExistingIds)->delete();
                    foreach ($request->external_link_ids as $key => $value) {
                        $labExternalLink = LabExternalLinks::select('id', 'social_media_link')->where([
                            ['lab_id', '=', $lab_id],
                            ['social_link_id', '=', $value],
                        ])->first();
                        if ($labExternalLink) {
                            if ($labExternalLink['social_media_link'] !== $request->external_links[$key]) {
                                $labExternalLink->social_media_link = $request->external_links[$key];
                                $labExternalLink->save();
                            }
                        }
                        if (!$labExternalLink) {
                            if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                                $labExternalLink = new LabExternalLinks();
                                $labExternalLink->lab_id = $lab_id;
                                $labExternalLink->social_media_link = $request->external_links[$key];
                                $labExternalLink->social_link_id = $value;
                                $labExternalLink->save();
                            }
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteLabExternalLinks($lab_id)
    {
        $checkExists = LabExternalLinks::select('id')->where('lab_id', $lab_id)->get()->toArray();
        if ($checkExists) {
            $deleteLabExternalLinks = LabExternalLinks::whereIn('id', $checkExists)->delete();
            if (!$deleteLabExternalLinks) {
                return false;
            }
        }

        return true;
    }
}
