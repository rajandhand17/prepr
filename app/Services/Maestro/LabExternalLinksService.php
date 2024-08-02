<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabExternalLinks;
use App\Models\SocialLink;

class LabExternalLinksService
{
    public static function createCloneLabExternalLinks($originalLabsTags, $clonedLabId)
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
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function createLabExternalLinks($request, $lab_id)
    {
        try {
            if (!empty($request->social_url) && !empty($request->lab_social)) {
                foreach ($request->social_url as $key => $value) {
                    if (!empty($request->social_url[$key]) && !empty($request->lab_social[$key])) {
                        LabExternalLinks::create([
                            'lab_id'            => $lab_id,
                            'social_link_id'    => $request->lab_social[$key],
                            'social_media_link' => $value,
                        ]);
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getLabExternalLinks($id)
    {
        try {
            $labSocialLinks = LabExternalLinks::where('lab_id', $id)->get();

            if ($labSocialLinks->isNotEmpty()) {
                foreach ($labSocialLinks as $link) {
                    $link->link_name = '';
                    $socialLink = SocialLink::where('id', $link->social_link_id)->first();

                    if ($socialLink) {
                        $link->link_name = $socialLink->title;
                    }
                }

                return $labSocialLinks;
            }

            return [];
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function updateLabExternalLinks($request, $id)
    {
        try {
            LabExternalLinks::where('lab_id', $id)->forceDelete();
            if (!empty(array_filter($request->social_url))) {
                foreach ($request->social_url as $key => $value) {
                    $lab_social_data['lab_id'] = $id;
                    $lab_social_data['social_link_id'] = $request->lab_social[$key];
                    $lab_social_data['social_media_link'] = $value;
                    LabExternalLinks::create($lab_social_data);
                }

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteLabExternalLinks($id)
    {
        try {
            if (LabExternalLinks::where('lab_id', $id)->delete()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
