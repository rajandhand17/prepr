<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Host;
use Exception;

class HostService
{
    public function getHosts($language = 'en', $search = null)
    {
        try {
            $host = Host::where('status','1')->select('id', 'title', 'link', 'image', 'status');
            if ($search != null) {
                $host = $host->where('title', 'like', '%'.$search.'%');
            }
            $host = $host->take(config('site-settings.dropdown_listing_limit'))->get();

            if (!$host->isEmpty()) {
                return $host;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSponsor($request)
    {
        try {
            return Host::where(['title' => $request->title, 'link' => $request->link])->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadSponsorMedia($image)
    {
        try {
            $upload_sponsor_image = FileUploadHelper::uploadImageToS3($image, 'sponsor_host');
            if ($upload_sponsor_image == false) {
                return false;
            }

            return $upload_sponsor_image;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createSponsor($request, $upload_sponsor_image)
    {
        try {
            $checkSponsor = Host::where(['title' => $request->title, 'link' => $request->link])->first();
            if (!$checkSponsor) {
                $newSponsor = new Host();
                $newSponsor->title = $request->title;
                $newSponsor->link = $request->link;
                $newSponsor->image = $upload_sponsor_image;
                $newSponsor->status = '1';
                $newSponsor->save();

                return $newSponsor;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
