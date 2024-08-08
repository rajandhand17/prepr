<?php

namespace App\Repositories\Api\ComponentAssociation;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Public\ChallengePathService;
use App\Services\Public\ChallengeService;
use App\Services\Public\LabProgramService;
use App\Services\Public\LabService;
use App\Services\Public\ResourceCollectionService;
use App\Services\Public\ResourceGroupService;
use App\Services\Public\ResourceModuleService;
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

    public function __construct(LabService $labService, LabProgramService $labProgramService, ChallengeService $challengeService, ChallengePathService $challengePathService, ResourceModuleService $resourceModuleService, ResourceCollectionService $resourceCollectionService, ResourceGroupService $resourceGroupService, ComponentAssociationService $componentAssociationService)
    {
        $this->labService = $labService;
        $this->labProgramService = $labProgramService;
        $this->challengeService = $challengeService;
        $this->challengePathService = $challengePathService;
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceCollectionService = $resourceCollectionService;
        $this->resourceGroupService = $resourceGroupService;
        $this->componentAssociationService = $componentAssociationService;
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
                $fetchLabProgramLabAssociation = $this->labProgramService->fetchLabProgramLabAssociation($request, $fetchLabProgramIdsAssociatedLabId);
            }

            return $fetchLabProgramLabAssociation;
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
                $fetchResourceGroupLabAssociation = $this->resourceGroupService->fetchResourceGroupLabAssociation($request, $fetchResourceGroupIdsAssociatedLabId);
            }

            return $fetchResourceGroupLabAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
