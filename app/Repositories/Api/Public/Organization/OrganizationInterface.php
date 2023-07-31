<?php

namespace App\Repositories\Api\Public\Organization;

interface OrganizationInterface
{
    public function getOrganizationList($request);
    public function getOrganizationBasedOnSlug($slug);

    public function checkFollowUnfollowExists($id,$action);

    public function checkLikeUnlikeExists($id,$action);

    public function follow($organization_id);

    public function unfollow($organization_id);
    public function like($organization_id);
    public function unlike($organization_id);
    public  function  share($organization_id);

}
