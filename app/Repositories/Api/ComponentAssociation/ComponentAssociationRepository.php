<?php

namespace App\Repositories\Api\ComponentAssociation;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengePathSkillsGroupsStackService;
use App\Services\Manage\ChallengeSkillsGroupsStackService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabProgramSkillsGroupsStackService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\ResourceCollectionSkillsGroupsStackService;
use App\Services\Manage\ResourceGroupSkillsGroupsStackService;
use App\Services\Manage\ResourceModuleSkillsGroupsStackService;
use App\Services\ProjectService;
use App\Services\Public\ChallengePathService;
use App\Services\Public\ChallengeService;
use App\Services\Public\LabProgramService;
use App\Services\Public\LabService;
use App\Services\Public\ResourceCollectionService;
use App\Services\Public\ResourceGroupService;
use App\Services\Public\ResourceModuleService;
use App\Services\SkillService;
use Exception;

class ComponentAssociationRepository implements ComponentAssociationInterface
{
    private $labService;
    private $labProgramService;
    private $challengeService;
    private $challengePathService;
    private $resourceModuleService;
    private $resourceCollectionService;
    private $resourceGroupService;
    private $componentAssociationService;
    private $labSkillsGroupsStackService;
    private $labProgramSkillsGroupsStackService;
    private $challengeSkillsGroupsStackService;
    private $challengePathSkillsGroupsStackService;
    private $resourceModuleSkillsGroupsStackService;
    private $resourceCollectionSkillsGroupsStackService;
    private $resourceGroupSkillsGroupsStackService;
    private $skillService;
    private $projectService;

    public function __construct(LabService $labService, LabProgramService $labProgramService, ChallengeService $challengeService, ChallengePathService $challengePathService, ResourceModuleService $resourceModuleService, ResourceCollectionService $resourceCollectionService, ResourceGroupService $resourceGroupService, ComponentAssociationService $componentAssociationService, LabSkillsGroupsStackService $labSkillsGroupsStackService, LabProgramSkillsGroupsStackService $labProgramSkillsGroupsStackService, ChallengeSkillsGroupsStackService $challengeSkillsGroupsStackService, ChallengePathSkillsGroupsStackService $challengePathSkillsGroupsStackService, ResourceModuleSkillsGroupsStackService $resourceModuleSkillsGroupsStackService, ResourceCollectionSkillsGroupsStackService $resourceCollectionSkillsGroupsStackService, ResourceGroupSkillsGroupsStackService $resourceGroupSkillsGroupsStackService, SkillService $skillService, ProjectService $projectService)
    {
        $this->labService = $labService;
        $this->labProgramService = $labProgramService;
        $this->challengeService = $challengeService;
        $this->challengePathService = $challengePathService;
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceCollectionService = $resourceCollectionService;
        $this->resourceGroupService = $resourceGroupService;
        $this->componentAssociationService = $componentAssociationService;
        $this->labSkillsGroupsStackService = $labSkillsGroupsStackService;
        $this->labProgramSkillsGroupsStackService = $labProgramSkillsGroupsStackService;
        $this->challengeSkillsGroupsStackService = $challengeSkillsGroupsStackService;
        $this->challengePathSkillsGroupsStackService = $challengePathSkillsGroupsStackService;
        $this->resourceModuleSkillsGroupsStackService = $resourceModuleSkillsGroupsStackService;
        $this->resourceCollectionSkillsGroupsStackService = $resourceCollectionSkillsGroupsStackService;
        $this->resourceGroupSkillsGroupsStackService = $resourceGroupSkillsGroupsStackService;
        $this->skillService = $skillService;
        $this->projectService = $projectService;
    }

    public function fetchLabs($request, $organizationId)
    {
        try {
            return $this->labService->getComponentBasedLabList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabLabProgramAssociation($request, $labProgramId)
    {
        try {
            $fetchLabLabProgramAssociation = collect();
            $fetchLabIdsAssociatedLabProgramId = $this->componentAssociationService->fetchLabIdsAssociatedLabProgramId($labProgramId);
            if ($fetchLabIdsAssociatedLabProgramId->isNotEmpty()) {
                $fetchLabLabProgramAssociation = $this->labService->fetchLabAssociation($request, $fetchLabIdsAssociatedLabProgramId);
            }

            return $fetchLabLabProgramAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabChallengeAssociation($request, $challengeId)
    {
        try {
            $fetchLabChallengeAssociation = collect();
            $fetchLabIdsAssociatedChallengeId = $this->componentAssociationService->fetchLabIdsAssociatedChallengeId($challengeId);
            if ($fetchLabIdsAssociatedChallengeId->isNotEmpty()) {
                $fetchLabChallengeAssociation = $this->labService->fetchLabAssociation($request, $fetchLabIdsAssociatedChallengeId);
            }

            return $fetchLabChallengeAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabResourceCollectionAssociation($request, $resourceCollectionId)
    {
        try {
            $fetchLabResourceCollectionAssociation = collect();
            $fetchLabIdsAssociatedResourceCollectionId = $this->componentAssociationService->fetchLabIdsAssociatedResourceCollectionId($resourceCollectionId);
            if ($fetchLabIdsAssociatedResourceCollectionId->isNotEmpty()) {
                $fetchLabResourceCollectionAssociation = $this->labService->fetchLabAssociation($request, $fetchLabIdsAssociatedResourceCollectionId);
            }

            return $fetchLabResourceCollectionAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabProgramLabAssociation($request, $labId)
    {
        try {
            $fetchLabProgramLabAssociation = collect();
            $fetchLabProgramIdsAssociatedLabId = $this->componentAssociationService->fetchLabProgramIdsAssociatedLabId($labId);
            if ($fetchLabProgramIdsAssociatedLabId->isNotEmpty()) {
                $fetchLabProgramLabAssociation = $this->labProgramService->fetchLabProgramAssociation($request, $fetchLabProgramIdsAssociatedLabId);
            }

            return $fetchLabProgramLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabProgramChallengeAssociation($request, $challengeId)
    {
        try {
            $fetchLabProgramChallengeAssociation = collect();
            $fetchLabProgramIdsAssociatedChallengeId = $this->componentAssociationService->fetchLabProgramIdsAssociatedChallengeId($challengeId);
            if ($fetchLabProgramIdsAssociatedChallengeId->isNotEmpty()) {
                $fetchLabProgramChallengeAssociation = $this->labProgramService->fetchLabProgramAssociation($request, $fetchLabProgramIdsAssociatedChallengeId);
            }

            return $fetchLabProgramChallengeAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabPrograms($request, $organizationId)
    {
        try {
            return $this->labProgramService->getComponentBasedLabProgramList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallenges($request, $organizationId)
    {
        try {
            return $this->challengeService->getComponentBasedChallengeList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengeLabAssociation($request, $labId)
    {
        try {
            $fetchChallengeLabAssociation = collect();
            $fetchChallengeIdsAssociatedLabId = $this->componentAssociationService->fetchChallengeIdsAssociatedLabId($labId);
            if ($fetchChallengeIdsAssociatedLabId->isNotEmpty()) {
                $fetchChallengeLabAssociation = $this->challengeService->fetchChallengeAssociation($request, $fetchChallengeIdsAssociatedLabId);
            }

            return $fetchChallengeLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengeResourceCollectionAssociation($request, $resourceCollectionId)
    {
        try {
            $fetchChallengeResourceCollectionAssociation = collect();
            $fetchChallengeIdsAssociatedResourceCollectionId = $this->componentAssociationService->fetchChallengeIdsAssociatedResourceCollectionId($resourceCollectionId);
            if ($fetchChallengeIdsAssociatedResourceCollectionId->isNotEmpty()) {
                $fetchChallengeResourceCollectionAssociation = $this->challengeService->fetchChallengeAssociation($request, $fetchChallengeIdsAssociatedResourceCollectionId);
            }

            return $fetchChallengeResourceCollectionAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengePathLabAssociation($request, $labId)
    {
        try {
            $fetchChallengePathLabAssociation = collect();
            $fetchChallengePathIdsAssociatedLabId = $this->componentAssociationService->fetchChallengePathIdsAssociatedLabId($labId);
            if ($fetchChallengePathIdsAssociatedLabId->isNotEmpty()) {
                $fetchChallengePathLabAssociation = $this->challengePathService->fetchChallengePathAssociation($request, $fetchChallengePathIdsAssociatedLabId);
            }

            return $fetchChallengePathLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengeChallengePathAssociation($request, $challengePathId)
    {
        try {
            $fetchChallengeChallengePathAssociation = collect();
            $fetchChallengeIdsAssociatedChallengePathId = $this->componentAssociationService->fetchChallengeIdsAssociatedChallengePathId($challengePathId);
            if ($fetchChallengeIdsAssociatedChallengePathId->isNotEmpty()) {
                $fetchChallengeChallengePathAssociation = $this->challengePathService->fetchChallengePathAssociation($request, $fetchChallengeIdsAssociatedChallengePathId);
            }

            return $fetchChallengeChallengePathAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengePaths($request, $organizationId)
    {
        try {
            return $this->challengePathService->getComponentBasedChallengePathList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModules($request, $organizationId)
    {
        try {
            return $this->resourceModuleService->getComponentBasedResourceModuleList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModuleLabAssociation($request, $labId)
    {
        try {
            $fetchResourceModuleLabAssociation = collect();
            $fetchResourceModuleIdsAssociatedLabId = $this->componentAssociationService->fetchResourceModuleIdsAssociatedLabId($labId);
            if ($fetchResourceModuleIdsAssociatedLabId->isNotEmpty()) {
                $fetchResourceModuleLabAssociation = $this->resourceModuleService->fetchResourceModuleAssociation($request, $fetchResourceModuleIdsAssociatedLabId);
            }

            return $fetchResourceModuleLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModuleChallengeAssociation($request, $challengeId)
    {
        try {
            $fetchResourceModuleChallengeAssociation = collect();
            $fetchResourceModuleIdsAssociatedChallengeId = $this->componentAssociationService->fetchResourceModuleIdsAssociatedChallengeId($challengeId);
            if ($fetchResourceModuleIdsAssociatedChallengeId->isNotEmpty()) {
                $fetchResourceModuleChallengeAssociation = $this->resourceModuleService->fetchResourceModuleAssociation($request, $fetchResourceModuleIdsAssociatedChallengeId);
            }

            return $fetchResourceModuleChallengeAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModuleResourceGroupAssociation($request, $resourceGroupId)
    {
        try {
            $fetchResourceModuleResourceGroupAssociation = collect();
            $fetchResourceModuleIdsAssociatedResourceGroupId = $this->componentAssociationService->fetchResourceModuleIdsAssociatedResourceGroupId($resourceGroupId);
            if ($fetchResourceModuleIdsAssociatedResourceGroupId->isNotEmpty()) {
                $fetchResourceModuleResourceGroupAssociation = $this->resourceModuleService->fetchResourceModuleAssociation($request, $fetchResourceModuleIdsAssociatedResourceGroupId);
            }

            return $fetchResourceModuleResourceGroupAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModuleResourceCollectionAssociation($request, $resourceCollectionId)
    {
        try {
            $fetchResourceModuleResourceCollectionAssociation = collect();
            $fetchResourceModuleIdsAssociatedResourceCollectionId = $this->componentAssociationService->fetchResourceModuleIdsAssociatedResourceCollectionId($resourceCollectionId);
            if ($fetchResourceModuleIdsAssociatedResourceCollectionId->isNotEmpty()) {
                $fetchResourceModuleResourceCollectionAssociation = $this->resourceModuleService->fetchResourceModuleAssociation($request, $fetchResourceModuleIdsAssociatedResourceCollectionId);
            }

            return $fetchResourceModuleResourceCollectionAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceCollections($request, $organizationId)
    {
        try {
            return $this->resourceCollectionService->getComponentBasedResourceCollectionList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceCollectionLabAssociation($request, $labId)
    {
        try {
            $fetchResourceCollectionLabAssociation = collect();
            $fetchResourceCollectionIdsAssociatedLabId = $this->componentAssociationService->fetchResourceCollectionIdsAssociatedLabId($labId);
            if ($fetchResourceCollectionIdsAssociatedLabId->isNotEmpty()) {
                $fetchResourceCollectionLabAssociation = $this->resourceCollectionService->fetchResourceCollectionAssociation($request, $fetchResourceCollectionIdsAssociatedLabId);
            }

            return $fetchResourceCollectionLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceCollectionResourceGroupAssociation($request, $resourceGroupId)
    {
        try {
            $fetchResourceCollectionResourceGroupAssociation = collect();
            $fetchResourceCollectionIdsAssociatedResourceGroupId = $this->componentAssociationService->fetchResourceCollectionIdsAssociatedResourceGroupId($resourceGroupId);
            if ($fetchResourceCollectionIdsAssociatedResourceGroupId->isNotEmpty()) {
                $fetchResourceCollectionResourceGroupAssociation = $this->resourceCollectionService->fetchResourceCollectionAssociation($request, $fetchResourceCollectionIdsAssociatedResourceGroupId);
            }

            return $fetchResourceCollectionResourceGroupAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceCollectionChallengeAssociation($request, $challengeId)
    {
        try {
            $fetchResourceCollectionChallengeAssociation = collect();
            $fetchResourceCollectionIdsAssociatedChallengeId = $this->componentAssociationService->fetchResourceCollectionIdsAssociatedChallengeId($challengeId);
            if ($fetchResourceCollectionIdsAssociatedChallengeId->isNotEmpty()) {
                $fetchResourceCollectionChallengeAssociation = $this->resourceCollectionService->fetchResourceCollectionAssociation($request, $fetchResourceCollectionIdsAssociatedChallengeId);
            }

            return $fetchResourceCollectionChallengeAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceGroups($request, $organizationId)
    {
        try {
            return $this->resourceGroupService->getComponentBasedResourceGroupList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceGroupLabAssociation($request, $labId)
    {
        try {
            $fetchResourceGroupLabAssociation = collect();
            $fetchResourceGroupIdsAssociatedLabId = $this->componentAssociationService->fetchResourceGroupIdsAssociatedLabId($labId);
            if ($fetchResourceGroupIdsAssociatedLabId->isNotEmpty()) {
                $fetchResourceGroupLabAssociation = $this->resourceGroupService->fetchResourceGroupAssociation($request, $fetchResourceGroupIdsAssociatedLabId);
            }

            return $fetchResourceGroupLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectLabAssociation($request, $labId)
    {
        try {
            $fetchProjectLabAssociation = $this->projectService->fetchProjectLabAssociation($labId);

            return $fetchProjectLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceGroupsBasedOnChallengeId($request, $challengeId)
    {
        try {
            $fetchResourceGroupsBasedOnChallengeId = collect();
            $fetchResourceGroupIdsAssociatedChallengeId = $this->componentAssociationService->fetchResourceGroupIdsAssociatedChallengeId($challengeId);
            if ($fetchResourceGroupIdsAssociatedChallengeId->isNotEmpty()) {
                $fetchResourceGroupsBasedOnChallengeId = $this->resourceGroupService->fetchResourceGroupAssociation($request, $fetchResourceGroupIdsAssociatedChallengeId);
            }

            return $fetchResourceGroupsBasedOnChallengeId;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRelatedLabs($labId)
    {
        try {
            $labs = collect();
            $labIds = $this->labSkillsGroupsStackService->getRecommendedLab($labId);
            if ($labIds) {
                $labs = $this->labService->getRelatedLabs($labIds);
            }

            return $labs;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRelatedLabPrograms($labProgramId)
    {
        try {
            $labPrograms = collect();
            $labProgramIds = $this->labProgramSkillsGroupsStackService->getRecommendedLabProgram($labProgramId);
            if ($labProgramIds) {
                $labPrograms = $this->labProgramService->getRelatedLabPrograms($labProgramIds);
            }

            return $labPrograms;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRelatedChallenges($challengeId)
    {
        try {
            $challenges = collect();
            $challengeIds = $this->challengeSkillsGroupsStackService->getRecommendedChallenge($challengeId);
            if ($challengeIds) {
                $challenges = $this->challengeService->getRelatedChallenges($challengeIds);
            }

            return $challenges;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRelatedChallengePaths($challengePathId)
    {
        try {
            $challengePaths = collect();
            $challengePathIds = $this->challengePathSkillsGroupsStackService->getRecommendedChallengePath($challengePathId);
            if ($challengePathIds) {
                $challengePaths = $this->challengePathService->getRelatedChallengePaths($challengePathIds);
            }

            return $challengePaths;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRelatedResourceModules($resourceModuleId)
    {
        try {
            $resourceModules = collect();
            $resourceModuleIds = $this->resourceModuleSkillsGroupsStackService->getRecommendedResourceModule($resourceModuleId);
            if ($resourceModuleIds) {
                $resourceModules = $this->resourceModuleService->getRelatedResourceModules($resourceModuleIds);
            }

            return $resourceModules;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRelatedResourceCollections($resourceCollectionId)
    {
        try {
            $resourceCollections = collect();
            $resourceCollectionIds = $this->resourceCollectionSkillsGroupsStackService->getRecommendedResourceCollection($resourceCollectionId);
            if ($resourceCollectionIds) {
                $resourceCollections = $this->resourceCollectionService->getRelatedResourceCollections($resourceCollectionIds);
            }

            return $resourceCollections;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRelatedResourceGroups($resourceGroupId)
    {
        try {
            $resourceGroups = collect();
            $resourceGroupIds = $this->resourceGroupSkillsGroupsStackService->getRecommendedResourceGroup($resourceGroupId);
            if ($resourceGroupIds) {
                $resourceGroups = $this->resourceGroupService->getRelatedResourceGroups($resourceGroupIds);
            }

            return $resourceGroups;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getSkillBasedOnId($skillId)
    {
        try {
            return $this->skillService->getSkillBasedOnId($skillId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengeSkillAssociation($request, $skillId)
    {
        try {
            $fetchChallengeSkillAssociation = collect();
            $fetchChallengeIdsAssociatedSkillId = $this->challengeSkillsGroupsStackService->fetchChallengeSkillAssociation($skillId);
            if ($fetchChallengeIdsAssociatedSkillId->isNotEmpty()) {
                $fetchChallengeSkillAssociation = $this->challengeService->fetchChallengeAssociation($request, $fetchChallengeIdsAssociatedSkillId);
            }

            return $fetchChallengeSkillAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchResourceModuleSkillAssociation($request, $resourceModuleId)
    {
        try {
            $fetchResourceModuleSkillAssociation = collect();
            $resourceModuleIds = $this->resourceModuleSkillsGroupsStackService->fetchResourceModuleSkillAssociation($resourceModuleId);
            if ($resourceModuleIds) {
                $fetchResourceModuleSkillAssociation = $this->resourceModuleService->fetchResourceModuleAssociation($request, $resourceModuleIds);
            }

            return $fetchResourceModuleSkillAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLabsBasedOnSkillId($request, $skillId)
    {
        try {
            $fetchLabSkillAssociation = collect();
            $LabIds = $this->labSkillsGroupsStackService->fetchLabSkillAssociation($skillId);
            if ($LabIds) {
                $fetchLabSkillAssociation = $this->labService->fetchLabAssociation($request, $LabIds);
            }

            return $fetchLabSkillAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
