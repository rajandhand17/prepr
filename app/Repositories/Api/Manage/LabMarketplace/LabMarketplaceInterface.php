<?php

namespace App\Repositories\Api\Manage\LabMarketplace;

interface LabMarketplaceInterface
{
    public function getLabMarketPlaceList($request);

    public function getLabBasedOnSlug($slug);

    public function getCheckLabUuid($uuid);

    public function getOrganizationIdBasedOnUuid($uuid);

    public function getLabMarketplaceBasedOnSlug($slug);

    public function addLabToMarketplace($slug, $labId);

    public function deleteLabMarketplace($slug, $labMarketplaceId);

    public function addLabRedeemData($labId, $organizationId, $labMarketplaceId);

    public function checkLabRedeemedOrNot($labMarketplaceId, $organizationId);

    public function labRedeem($labMarketplaceId, $organizationId);

    public function addLabRedeemed($labMarketplaceId, $organizationId, $labId);
}
