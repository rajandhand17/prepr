<?php

namespace App\Traits\Maestro\LabMarketplace;

use App\Services\Maestro\LabMarketplace\LabMarketplaceService;

trait LabMarketplaceTrait
{
    private function getLabMarketplace()
    {
        try {
            $labMarketplace = LabMarketplaceService::getLabMarketplace();
            if ($labMarketplace) {
                return $labMarketplace;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteLabMarketplaceById($id)
    {
        try {
            $slug = LabMarketplaceService::getLabMarketplaceBasedOnId($id)->slug;
            $deleteLabMarketplace = LabMarketplaceService::deleteLabMarketplace($slug, $id);
            if ($deleteLabMarketplace) {
                return $deleteLabMarketplace;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
