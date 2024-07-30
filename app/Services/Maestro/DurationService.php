<?php

namespace App\Services\Maestro;

use App\Models\Duration;
use Exception;

class DurationService
{
    public static function getLevelById($duration_id)
    {
        try {
            return Duration::where(['id' => $duration_id])->pluck('title', 'id');
        } catch(Exception $e) {
            return false;
        }
    }

    public static function getDurationsById($request)
    {
        try {
            if ($request->language == 'en') {
                $durationQuery = Duration::select('id', 'title')->orderBy('id', 'DESC')->take(30);
                $columName = 'title';
            } else {
                $columName = $request->language;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName.'_title';
                $durationQuery = Duration::select('id', $columName.' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $durationQuery->where($columName, 'LIKE', '%'.$request->search.'%');
            }
            $durationQuery = $durationQuery->pluck('title', 'id');
            $durationsArray = $jsonDurations = [];
            $count = 0;
            foreach ($durationQuery as $key => $duration) {
                $durationsArray[$count]['id'] = $key;
                $durationsArray[$count]['text'] = $duration;
                $count++;
            }
            $jsonDurations['result'] = $durationsArray;
            $jsonDurations['more'] = true;
            $jsonDurations['total_count'] = $durationQuery->count();

            return response()->json($jsonDurations);
        } catch (Exception $e) {
            return false;
        }
    }
}
