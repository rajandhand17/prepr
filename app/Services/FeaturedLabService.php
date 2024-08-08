<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\FeaturedLab;

class FeaturedLabService
{
    public function getFeaturedLab()
    {
        try {
            return FeaturedLab::get()->take(6);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getFeaturedLabBasedOnLabId($id)
    {
        try {
            return FeaturedLab::where('lab_id', $id)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function createFeaturedLab($lab)
    {
        try {
            $featuredLab = FeaturedLab::where('lab_id', $lab->id)->first();
            if (!$featuredLab) {
                $featuredLab = new FeaturedLab();
                $featuredLab->lab_id = $lab->id;
                $featuredLab->save();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
