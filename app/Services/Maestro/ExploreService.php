<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Explore;
use Exception;
use Schema;

class ExploreService
{
    public static function updateExploreDataById($id, $request)
    {
        try {
            $exploreData = Explore::find($id);
            $input = $request->all();
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('explore_page_data', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            if (!empty($insertArray)) {
                Explore::where('id', $id)->update($insertArray);
                if ($request->media) {
                    $cover_Image = FileUploadHelper::uploadImageToS3($request->file('media'), 'explore');
                    $exploreData->media = $cover_Image ? $cover_Image : 'NULL';
                }
                if ($request->roles) {
                    $exploreData->role = json_encode($request->roles);
                }
                $exploreData->save();

                return true;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteExploreData($id)
    {
        try {
            $Explore = Explore::find($id);

            if ($Explore) {
                return $Explore->delete();
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getExploreData()
    {
        try {
            return Explore::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function insertExploreDatas($request)
    {
        try {
            $namespace = 'App\\Models\\';
            $class = $namespace.$request->compType;
            $componentRequest = resolve($class)->where('id', $request->compId)->first();
            // Limit the description to 200 words
            $description = substr($componentRequest->description, 0, 200);

            Explore::create([
                'comp_type'    => $request->compType,
                'comp_id'      => $request->compId,
                'role'         => '["user"]',
                'title'        => $componentRequest->title,
                'description'  => $description,
                'action_button'=> 'View',
                'media_type'   => $componentRequest->media_type,
                'media'        => $componentRequest->media,
            ]);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
