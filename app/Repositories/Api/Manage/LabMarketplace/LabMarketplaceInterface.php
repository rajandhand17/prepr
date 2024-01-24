<?php

namespace App\Repositories\Api\Manage\LabMarketplace;

interface LabMarketplaceInterface
{
    public function getLabBasedOnSlug($slug);
    
    public function getCheckLabUuid($uuid);
    
    public function getOrganizationIdBasedOnUuid($uuid);
    
    public function getLabMarketplaceBasedOnSlug($slug);
    
    public function createLabMarketplace($slug, $labId, $organizationId);
    
    public function deleteLabMarketplace($slug, $labMarketplaceId);
}
