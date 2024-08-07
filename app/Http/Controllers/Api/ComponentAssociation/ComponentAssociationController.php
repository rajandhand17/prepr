<?php

namespace App\Http\Controllers\Api\ComponentAssociation;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Http\Resources\Public\ChallengePath\ChallengePathResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Http\Resources\Public\LabProgram\LabProgramResource;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionResource;
use App\Http\Resources\Public\ResourceGroup\ResourceGroupResource;
use App\Http\Resources\Public\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\ComponentAssociation\ComponentAssociationRepository;
use Exception;
use Illuminate\Http\Request;

class ComponentAssociationController extends AppBaseController
{
    private $componentAssociationRepository;

    public function __construct(ComponentAssociationRepository $componentAssociationRepository)
    {
        $this->componentAssociationRepository = $componentAssociationRepository;
    }

    public function getComponentAssociationBasedOnOtherComponent($component, $slug, $type, Request $request)
    {
        try {
            if (!in_array($component, ['organization', 'lab', 'lab-program', 'challenge', 'challenge-path', 'resource-module', 'resource-collection', 'resource-group'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            if (!in_array($type, ['organization', 'lab', 'lab-program', 'challenge', 'challenge-path', 'resource-module', 'resource-collection', 'resource-group'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 404);
            }

            $response = [];
            $message = __('responses.no_association_founc');
            switch ($component) {
                case 'organization':
                    switch ($type) {
                        case 'lab':
                            $fetchLabs = self::fetchLabsBasedOnOrganizationId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchLabs['response'];
                            $message = $fetchLabs['message'];
                            break;

                        case 'lab-program':
                            $fetchLabPrograms = self::fetchLabProgramsBasedOnOrganizationId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchLabPrograms['response'];
                            $message = $fetchLabPrograms['message'];
                            break;

                        case 'challenge':
                            $fetchChallenges = self::fetchChallengesBasedOnOrganizationId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchChallenges['response'];
                            $message = $fetchChallenges['message'];
                            break;

                        case 'challenge-path':
                            $fetchChallengePaths = self::fetchChallengePathsBasedOnOrganizationId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchChallengePaths['response'];
                            $message = $fetchChallengePaths['message'];
                            break;

                        case 'resource-module':
                            $fetchResourceModules = self::fetchResourceModulesBasedOnOrganizationId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchResourceModules['response'];
                            $message = $fetchResourceModules['message'];
                            break;

                        case 'resource-collection':
                            $fetchResourceCollections = self::fetchResourceCollectionsBasedOnOrganizationId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchResourceCollections['response'];
                            $message = $fetchResourceCollections['message'];
                            break;

                        case 'resource-group':
                            $fetchResourceGroups = self::fetchResourceGroupsBasedOnOrganizationId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchResourceGroups['response'];
                            $message = $fetchResourceGroups['message'];
                            break;
                    }
                    break;

                case 'lab':
                    switch ($type) {
                        case 'challenge':
                            $fetchChallenges = self::fetchChallengesBasedOnLabId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchChallenges['response'];
                            $message = $fetchChallenges['message'];
                            break;

                        case 'resource-module':
                            $fetchResourceModules = self::fetchResourceModulesBasedOnLabId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchResourceModules['response'];
                            $message = $fetchResourceModules['message'];
                            break;
                    }
                    break;

                case 'lab-program':
                    switch ($type) {
                        case 'lab':
                            $fetchLabs = self::fetchLabsBasedOnLabProgramId($request, $checkComponentBasedOnSlug->id);
                            $response = $fetchLabs['response'];
                            $message = $fetchLabs['message'];
                            break;
                    }
                    break;
            }

            return $this->sendResponse($response, $message, 200);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchLabsBasedOnLabProgramId($request, $labProgramId)
    {
        try {
            $fetchLabs = $this->componentAssociationRepository->fetchLabLabProgramAssociation($request, $labProgramId);
            $data = self::labResponse($fetchLabs);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchLabsBasedOnOrganizationId($request, $organizationId)
    {
        try {
            $fetchLabs = $this->componentAssociationRepository->fetchLabs($request, $organizationId);
            $data = self::labResponse($fetchLabs);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function labResponse($labData)
    {
        try {
            $response = [];
            $message = __('responses.no_association_found');
            if ($labData != false) {
                $response = [
                    'total_count'  => $labData->total(),
                    'per_page'     => $labData->perPage(),
                    'count'        => $labData->count(),
                    'current_page' => $labData->currentPage(),
                    'total_pages'  => $labData->lastPage(),
                    'list'         => LabResource::collection($labData),
                ];
                $message = __('responses.found_labs_list');
            }

            return ['response' => $response, 'message' => $message];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchLabProgramsBasedOnOrganizationId($request, $organizationId)
    {
        try {
            $fetchLabPrograms = $this->componentAssociationRepository->fetchLabPrograms($request, $organizationId);
            $data = self::labProgramResponse($fetchLabPrograms);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function labProgramResponse($labProgramData)
    {
        try {
            $response = [];
            $message = __('responses.no_association_found');
            if ($labProgramData !== false) {
                $response = [
                    'total_count'  => $labProgramData->total(),
                    'per_page'     => $labProgramData->perPage(),
                    'count'        => $labProgramData->count(),
                    'current_page' => $labProgramData->currentPage(),
                    'total_pages'  => $labProgramData->lastPage(),
                    'list'         => LabProgramResource::collection($labProgramData),
                ];

                $message = __('responses.found_lab_program_list');
            }

            return ['response' => $response, 'message' => $message];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchChallengesBasedOnOrganizationId($request, $organizationId)
    {
        try {
            $fetchChallenges = $this->componentAssociationRepository->fetchChallenges($request, $organizationId);
            $data = self::challengeResponse($fetchChallenges);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchChallengesBasedOnLabId($request, $labId)
    {
        try {
            $fetchChallenges = $this->componentAssociationRepository->fetchChallengeLabAssociation($request, $labId);
            $data = self::challengeResponse($fetchChallenges);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function challengeResponse($challengeData)
    {
        try {
            $response = [];
            $message = __('responses.no_association_found');
            if ($challengeData !== false) {
                $response = [
                    'total_count'  => $challengeData->total(),
                    'per_page'     => $challengeData->perPage(),
                    'count'        => $challengeData->count(),
                    'current_page' => $challengeData->currentPage(),
                    'total_pages'  => $challengeData->lastPage(),
                    'list'         => ChallengeResource::collection($challengeData),
                ];

                $message = __('responses.found_challenges_list');
            }

            return ['response' => $response, 'message' => $message];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchChallengePathsBasedOnOrganizationId($request, $organizationId)
    {
        try {
            $fetchChallengePaths = $this->componentAssociationRepository->fetchChallengePaths($request, $organizationId);
            $data = self::challengePathResponse($fetchChallengePaths);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function challengePathResponse($challengePathData)
    {
        try {
            $response = [];
            $message = __('responses.no_association_found');
            if ($challengePathData !== false) {
                $response = [
                    'total_count'  => $challengePathData->total(),
                    'per_page'     => $challengePathData->perPage(),
                    'count'        => $challengePathData->count(),
                    'current_page' => $challengePathData->currentPage(),
                    'total_pages'  => $challengePathData->lastPage(),
                    'list'         => ChallengePathResource::collection($challengePathData),
                ];

                $message = __('responses.found_challenge_path_list');
            }

            return ['response' => $response, 'message' => $message];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchResourceModulesBasedOnOrganizationId($request, $organizationId)
    {
        try {
            $fetchResourceModules = $this->componentAssociationRepository->fetchResourceModules($request, $organizationId);
            $data = self::resourceModulesResponse($fetchResourceModules);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchResourceModulesBasedOnLabId($request, $labId)
    {
        try {
            $fetchResourceModules = $this->componentAssociationRepository->fetchResourceModuleLabAssociation($request, $labId);
            $data = self::resourceModulesResponse($fetchResourceModules);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function resourceModulesResponse($resourceModuleData)
    {
        try {
            $response = [];
            $message = __('responses.no_association_found');
            if ($resourceModuleData !== false) {
                $response = [
                    'total_count'  => $resourceModuleData->total(),
                    'per_page'     => $resourceModuleData->perPage(),
                    'count'        => $resourceModuleData->count(),
                    'current_page' => $resourceModuleData->currentPage(),
                    'total_pages'  => $resourceModuleData->lastPage(),
                    'list'         => ResourceModuleResource::collection($resourceModuleData),
                ];

                $message = __('responses.found_resource_module_list');
            }

            return ['response' => $response, 'message' => $message];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchResourceCollectionsBasedOnOrganizationId($request, $organizationId)
    {
        try {
            $fetchResourceCollections = $this->componentAssociationRepository->fetchResourceCollections($request, $organizationId);
            $data = self::resourceCollectionsResponse($fetchResourceCollections);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function resourceCollectionsResponse($resourceCollectionData)
    {
        try {
            $response = [];
            $message = __('responses.no_association_found');
            if ($resourceCollectionData !== false) {
                $response = [
                    'total_count'  => $resourceCollectionData->total(),
                    'per_page'     => $resourceCollectionData->perPage(),
                    'count'        => $resourceCollectionData->count(),
                    'current_page' => $resourceCollectionData->currentPage(),
                    'total_pages'  => $resourceCollectionData->lastPage(),
                    'list'         => ResourceCollectionResource::collection($resourceCollectionData),
                ];

                $message = __('responses.found_resource_collection_list');
            }

            return ['response' => $response, 'message' => $message];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchResourceGroupsBasedOnOrganizationId($request, $organizationId)
    {
        try {
            $fetchResourceGroups = $this->componentAssociationRepository->fetchResourceGroups($request, $organizationId);
            $data = self::resourceGroupsResponse($fetchResourceGroups);

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function resourceGroupsResponse($resourceGroupData)
    {
        try {
            $response = [];
            $message = __('responses.no_association_found');
            if ($resourceGroupData !== false) {
                $response = [
                    'total_count'  => $resourceGroupData->total(),
                    'per_page'     => $resourceGroupData->perPage(),
                    'count'        => $resourceGroupData->count(),
                    'current_page' => $resourceGroupData->currentPage(),
                    'total_pages'  => $resourceGroupData->lastPage(),
                    'list'         => ResourceGroupResource::collection($resourceGroupData),
                ];

                $message = __('responses.found_resource_group_list');
            }

            return ['response' => $response, 'message' => $message];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
