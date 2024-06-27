<?php

namespace App\Services\Maestro\Sponsor;

use App\Models\Host;
use Exception;

class SponsorService
{
    public static function getSponsorList()
    {
        try {
            return Host::latest();
        } catch (Exception $e) {
            return false;
        }
    }
    public static function createSponsor($request)
    {
        try {
            $sponsorImage = null;
            if ($request->file('image')) {
                $sponsorImage = $request->file('image')->store('uploads/hosts', 's3');
            }
            return Host::create(['title' => $request->title, 'link' => $request->link, 'image' => $sponsorImage, 'status' => $request->status]);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function deleteSponsor($id)
    {
        try {
            $sponsor = Host::find($id);
            if (!empty($sponsor)) {
                return $sponsor->delete();
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getSponsorStatus()
    {
        try {
            return ['1' => 'Active', '0' => 'Not Active'];
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getSponsorById($id)
    {
        try {
            $sponsor = Host::findOrFail($id);
            if ($sponsor != null) {
                return $sponsor;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function updateSponsorById($id, $request)
    {
        try {
            $sponsor = Host::findOrFail($id);
            if (!empty($sponsor)) {
                    if ($request->file('image')) {
                        $sponsor->image = $request->file('image')->store('uploads/hosts', 's3');
                    }
                    $sponsor->title  = $request->title;
                    $sponsor->link   = $request->link;
                    $sponsor->status   = $request->status;
                if ($sponsor->save()) {
                    return true;
                }
                return false;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
