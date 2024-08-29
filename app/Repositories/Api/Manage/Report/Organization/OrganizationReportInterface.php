<?php

namespace App\Repositories\Api\Manage\Report\Organization;

use App\Models\Organization;

interface OrganizationReportInterface
{
    public function getPaginatedChallenges(Organization $organization): false|array;

    public function getOrganizationEngagements(Organization $organization): false|array;

    public function getOrganizationMembers(Organization $organization): false|array;

    public function getPaginatedChallengePath(Organization $organization): false|array;

    public function getPaginatedResourceModule(Organization $organization): false|array;

    public function getPaginatedLabs(Organization $organization): false|array;

    public function getPaginatedLabPrograms(Organization $organization): false|array;

    public function getPaginatedResourceCollection(Organization $organization): false|array;

    public function getPaginatedResourceGroup(Organization $organization): false|array;

    public function getOrganizationMemberActivity(Organization $organization): false|array;
}
