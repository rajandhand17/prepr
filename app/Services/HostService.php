<?php

namespace App\Services;

use App\Models\Host;
use Exception;

class HostService
{
    public function getHosts($language = 'en', $search = null)
    {
        try {
            $host = Host::select('id', 'title', 'link', 'image', 'status');
            if ($search != null) {
                $host = $host->where('title', 'like', '%'.$search.'%');
            }
            $host = $host->take(config('site-settings.dropdown_listing_limit'))->get();
            //  return $host;
            if (!$host->isEmpty()) {
                return $host;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSponsor($request)
    {
        try {
            return Host::where(['title' => $request->title, 'link' => $request->link])->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function createSponsor($request)
    {
        try {
            $checkSponsor = Host::where(['title' => $request->title, 'link' => $request->link])->first();
            if (!$checkSponsor) {
                $newSponsor = new Host();
                $newSponsor->title = $request->title;
                $newSponsor->link = $request->link;
                $newSponsor->image = $request->image;
                $newSponsor->status = '1';
                $newSponsor->save();

                return $newSponsor;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
