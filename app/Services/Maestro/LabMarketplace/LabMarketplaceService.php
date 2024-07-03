<?php

namespace App\Services\Maestro\LabMarketplace;

use App\Models\LabMarketplace;
use Exception;

class LabMarketplaceService
{
    public static function getLabMarketplace()
    {
        try {
            return LabMarketplace::where('language', \Session::get('globalLocale') ? \Session::get('globalLocale') : 'en')->orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getLabMarketplaceBasedOnId($id)
    {
        try {
            return LabMarketplace::where('id', $id)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteLabMarketplace($id)
    {
        try {
        } catch (Exception $e) {
            return false;
        }
    }
}
