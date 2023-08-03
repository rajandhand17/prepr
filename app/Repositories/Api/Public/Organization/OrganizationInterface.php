<?php

namespace App\Repositories\Api\Public\Organization;

interface OrganizationInterface
{
    public function getOrganizationList($request);

    public function getOrganizationBasedOnSlug($slug);
    public function organizationSocialActivitiesService($id,$column,$action);
    public function checkExists($id, $action);

}
