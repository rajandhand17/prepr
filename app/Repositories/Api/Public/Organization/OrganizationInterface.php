<?php

namespace App\Repositories\Api\Public\Organization;

interface OrganizationInterface
{
    public function getList($request);

    public function getOrganizationBasedOnSlug($slug);
    public function socialActivity($id,$column,$action);
    public function checkSocialActivity($lab_id,$action);
}
