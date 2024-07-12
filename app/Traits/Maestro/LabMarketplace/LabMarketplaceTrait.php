<?php

namespace App\Traits\Maestro\LabMarketplace;

use App\Services\Maestro\LabMarketplaceService;
use Illuminate\Support\Facades\DB;

trait LabMarketplaceTrait
{
    private function getLabMarketplace()
    {
        try {
            $labMarketplace =  $this->labMarketplaceService->getLabMarketplace();
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
            $slug = $this->labMarketplaceService->getLabMarketplaceBasedOnId($id)->slug;
            $deleteLabMarketplace =  $this->labMarketplaceService->deleteLabMarketplace($slug, $id);
            if ($deleteLabMarketplace) {
                return $deleteLabMarketplace;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabMarketplaceById($id)
    {
        try {
            $labMarketplace = $this->labMarketplaceService->getLabMarketplaceBasedOnId($id);
            if($labMarketplace){
                return $labMarketplace;
            }
        }catch (\Exception $e) {
            return false;
        }
    }
}
