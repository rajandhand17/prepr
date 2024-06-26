<?php

namespace App\Services\Maestro\LabMarketplace;

use App\Models\LabMarketplace;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class LabMarketplaceService
{
    public static function getLabMarketplace()
    {
        try {
            return LabMarketplace::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteLabMarketplace($id)
    {
        try {

        }catch (Exception $e) {
            return false;
        }
    }
}
