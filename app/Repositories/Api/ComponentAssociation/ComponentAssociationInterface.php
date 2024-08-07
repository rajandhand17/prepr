<?php

namespace App\Repositories\Api\ComponentAssociation;

interface ComponentAssociationInterface
{
    public function fetchLabs($request, $organizationId);

    public function fetchLabLabProgramAssociation($request, $labProgramId);

    public function fetchLabPrograms($request, $organizationId);

    public function fetchChallenges($request, $organizationId);

    public function fetchChallengeLabAssociation($request, $componentId);

    public function fetchChallengePaths($request, $organizationId);

    public function fetchResourceModules($request, $organizationId);

    public function fetchResourceModuleLabAssociation($request, $labId);

    public function fetchResourceCollections($request, $organizationId);

    public function fetchResourceGroups($request, $organizationId);
}
