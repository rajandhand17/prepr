<?php

namespace App\Services\Maestro\Explore;

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
                    $cover_Image = $request->file('media')->store('uploads/explore', 's3');
                    $exploreData->media = $cover_Image ? $cover_Image : 'NULL';
                }
                if ($request->roles) {
                    $exploreData->role = $request->roles;
                }
                $exploreData->save();
                return true;
            }
        } catch (Exception $e) {
            dd($e);
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
            return false;
        }
    }

    public static function getExploreData()
    {
        try {
            return Explore::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
