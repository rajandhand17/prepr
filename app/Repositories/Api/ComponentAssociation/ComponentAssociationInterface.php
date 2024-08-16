<?php

namespace App\Repositories\Api\ComponentAssociation;

interface ComponentAssociationInterface
{
    public function fetchLabs($request, $organizationId);

    public function fetchLabLabProgramAssociation($request, $labProgramId);

    public function fetchLabChallengeAssociation($request, $challengeId);

    public function fetchLabResourceCollectionAssociation($request, $resourceCollectionId);

    public function fetchLabProgramLabAssociation($request, $labId);

    public function fetchLabProgramChallengeAssociation($request, $challengeId);

    public function fetchLabPrograms($request, $organizationId);

    public function fetchChallenges($request, $organizationId);

    public function fetchChallengeLabAssociation($request, $labId);

    public function fetchChallengeResourceCollectionAssociation($request, $resourceCollectionId);

    public function fetchChallengePathLabAssociation($request, $labId);

    public function fetchChallengeChallengePathAssociation($request, $challengePathId);

    public function fetchChallengePaths($request, $organizationId);

    public function fetchResourceModules($request, $organizationId);

    public function fetchResourceModuleLabAssociation($request, $labId);

    public function fetchResourceModuleChallengeAssociation($request, $challengeId);

    public function fetchResourceModuleResourceGroupAssociation($request, $resourceGroupId);

    public function fetchResourceModuleResourceCollectionAssociation($request, $resourceCollectionId);

    public function fetchResourceCollections($request, $organizationId);

    public function fetchResourceCollectionLabAssociation($request, $labId);

    public function fetchResourceCollectionResourceGroupAssociation($request, $resourceGroupId);

    public function fetchResourceCollectionChallengeAssociation($request, $challengeId);

    public function fetchResourceGroups($request, $organizationId);

    public function fetchResourceGroupLabAssociation($request, $labId);

    public function fetchResourceGroupsBasedOnChallengeId($request, $challengeId);

    public function fetchRelatedLabs($labId);

    public function fetchRelatedLabPrograms($labProgramId);

    public function fetchRelatedChallenges($challengeId);

    public function fetchRelatedChallengePaths($challengePathId);

    public function fetchRelatedResourceModules($resourceModuleId);

    public function fetchRelatedResourceCollections($resourceCollectionId);

    public function fetchRelatedResourceGroups($resourceGroupId);

    public function getSkillBasedOnId($skillId);

    public function fetchChallengeSkillAssociation($request, $skillId);

    public function fetchResourceModuleSkillAssociation($request, $resourceModuleId);

    public function fetchLabsBasedOnSkillId($request, $skillId);
}
