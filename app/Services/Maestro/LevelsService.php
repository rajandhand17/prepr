<?php

namespace App\Services\Maestro;

use App\Models\Levels;
use Exception;

class LevelsService
{
    public static function getLevelById($level_id)
    {
        try {
            return Levels::where(['id' => $level_id])->pluck('title', 'id');
        } catch(Exception $e) {
            return false;
        }
    }

    public static function getLevelsById($request)
    {
        try {
            if ($request->language == 'en') {
                $levelsQuery = Levels::select('id', 'title')->orderBy('id', 'DESC')->take(30);
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
                $levelsQuery = Levels::select('id', $columName.' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $levelsQuery->where($columName, 'LIKE', '%'.$request->search.'%');
            }
            $levelsQuery = $levelsQuery->pluck('title', 'id');
            $levelsArray = $jsonLevels = [];
            $count = 0;
            foreach ($levelsQuery as $key => $level) {
                $levelsArray[$count]['id'] = $key;
                $levelsArray[$count]['text'] = $level;
                $count++;
            }
            $jsonLevels['result'] = $levelsArray;
            $jsonLevels['more'] = true;
            $jsonLevels['total_count'] = $levelsQuery->count();

            return response()->json($jsonLevels);
        } catch (Exception $e) {
            return false;
        }
    }
}
