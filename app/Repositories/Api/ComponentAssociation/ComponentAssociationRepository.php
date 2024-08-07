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
                $fetchLabLabProgramAssociation = $this->labService->fetchLabLabProgramAssociation($request, $fetchLabIdsAssociatedLabProgramId);
            }

            return $fetchLabLabProgramAssociation;
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

    public function fetchChallengeLabAssociation($request, $componentId)
    {
        try {
            $fetchChallengeLabAssociation = collect();
            $fetchChallengeIdsAssociatedLabId = $this->componentAssociationService->fetchChallengeIdsAssociatedLabId($componentId);
            if ($fetchChallengeIdsAssociatedLabId->isNotEmpty()) {
                $fetchChallengeLabAssociation = $this->challengeService->fetchChallengeLabAssociation($request, $fetchChallengeIdsAssociatedLabId);
            }

            return $fetchChallengeLabAssociation;
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
                $fetchResourceModuleLabAssociation = $this->resourceModuleService->fetchResourceModuleLabAssociation($request, $fetchResourceModuleIdsAssociatedLabId);
            }

            return $fetchResourceModuleLabAssociation;
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

    public function fetchResourceGroups($request, $organizationId)
    {
        try {
            return $this->resourceGroupService->getComponentBasedResourceGroupList($request, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
