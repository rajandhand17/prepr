<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\FeaturedModule;
use Exception;
use Schema;

class ExploreService
{
    public static function updateExploreDataById($id, $request)
    {
        try {
            $exploreData = FeaturedModule::find($id);
            $input = $request->all();
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('explore_page_data', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            if (!empty($insertArray)) {
                FeaturedModule::where('id', $id)->update($insertArray);
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
            $Explore = FeaturedModule::find($id);

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
            return FeaturedModule::orderBy('id', 'desc');
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

            switch($request->compType) {
                case 'Lab':   // Labs
                    $moduleType = '0';
                    break;
                case 'Lab Program': // Lab Program
                    $moduleType = '1';
                    break;
                case 'Challenge': // Challenge
                    $moduleType = '2';
                    break;
                case 'Challenge Path': // Challenge Path
                    $moduleType = '3';
                    break;
                case 'Resource Module': // Resource Module
                    $moduleType = '4';
                    break;
                case 'Resource Collection': // Resource Collection
                    $moduleType = '5';
                    break;
                case 'Resource Group': // Resource Collection
                    $moduleType = '6';
                    break;
                case 'Project': // project 
                    $moduleType = '7';
                    break;
            }
            if ($componentRequest->media) {
                $start =strpos($componentRequest->media, 'uploads');
                if ($start !== false) {
                    $uploadsPath = substr($componentRequest->media, $start);
                }
            } else {
                $uploadsPath = null;
            }
           
            FeaturedModule::create([
                'module_type'    => $moduleType,
                'module_id'      => $request->compId,
                'role'         => '["user"]',
                'title'        => $componentRequest->title,
                'description'  => $description,
                'button_text'=> 'View',
                'media_type'   => $componentRequest->media_type,
                'media'        => $uploadsPath,
            ]);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
