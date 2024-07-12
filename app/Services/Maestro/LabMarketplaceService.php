<?php

namespace App\Services\Maestro;

use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
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

    public static function deleteLabMarketplace($slug, $labMarketplaceId)
    {
        try {
            // Deleting lab marketplace
            $labMarketplace = LabMarketplace::where('slug', $slug)->delete();
            if ($labMarketplace) {
                // Triggered LabMarketplace related data deletion event
                 event(new DeleteLabMarketplaceAssociatedData($labMarketplaceId));
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
