<?php

namespace App\Services;

use App\Models\Host;

class HostService
{
    public function getHosts($language = 'en', $search = null)
    {
        try {
            $host = Host::select('id', 'title', 'link', 'image', 'status');
            if ($search != null) {
                $host = $host->where('title', 'like', '%'.$search.'%');
            }
            $host = $host->take(20)->get();
            //  return $host;
            if (!$host->isEmpty()) {
                return $host;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
