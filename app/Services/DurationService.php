<?php

namespace App\Services;

use App\Models\Duration;

class DurationService
{
    public function getDurations($language = 'en', $search = null)
    {
        try {
            $durations = Duration::select('title', 'fr_CA_title');
            if ($durations) {
                $durations = $durations->where('title', 'like', '%'.$search.'%');
            }
            $durations = $durations->get();
            return $durations;
        } catch(\Exception $e) {
            return false;
        }
    }
}
