<?php

namespace App\Http\Controllers\Api\Public\AdvanceSearch;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Public\AdvanceSearch\ChallengePathSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\ChallengeSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\ChallengeTemplateSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\LabMarketplaceSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\LabProgramSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\LabSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\OrganizationSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\ProjectSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\ResourceCollectionSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\ResourceGroupSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\ResourceModuleSearchRequest;
use App\Http\Requests\Public\AdvanceSearch\UserSearchRequest;
use App\Http\Resources\Manage\ChallengeTemplate\ChallengeTemplateResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Http\Resources\Public\ChallengePath\ChallengePathResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Http\Resources\Public\LabProgram\LabProgramResource;
use App\Http\Resources\Public\Organization\OrganizationResource;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionResource;
use App\Http\Resources\Public\ResourceGroup\ResourceGroupResource;
use App\Http\Resources\Public\ResourceModule\ResourceModuleResource;
use App\Http\Resources\User\UserResource;
use App\Repositories\Api\Public\AdvanceSearch\AdvanceSearchRepository;

class AdvanceSearchController extends AppBaseController
{
    /**
     * @param AdvanceSearchRepository $advanceSearchRepository
     */
    public function __construct(protected AdvanceSearchRepository $advanceSearchRepository)
    {
    }

    public function searchLab(LabSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchLab($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_lab_list'));
            }

            return $this->sendResponse([
                ...$data,
                'list' => LabResource::collection(data_get($data, 'list')),
            ], __('responses.advance_search.lab_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_lab_list'));
        }
    }

    public function searchLabPrograms(LabProgramSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchLabPrograms($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_lab_programs_list'));
            }

            return $this->sendResponse([
                ...$data,
                'list' => LabProgramResource::collection(data_get($data, 'list')->items()),
            ], __('responses.advance_search.lab_programs_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_lab_programs_list'));
        }
    }

    public function searchLabMarketPlace(LabMarketplaceSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchLabMarketPlace($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_lab_marketplace_list'));
            }

            return $this->sendResponse([...$data, 'list' => data_get($data, 'list')->items()], __('responses.advance_search.lab_marketplace_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_lab_marketplace_list'));
        }
    }

    public function searchChallenges(ChallengeSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchChallenges($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_challenges_list'));
            }

            return $this->sendResponse([...$data, 'list' => ChallengeResource::collection(data_get($data, 'list'))], __('responses.advance_search.challenges_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_challenges_list'));
        }
    }

    public function searchChallengePaths(ChallengePathSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchChallengePaths($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_challenge_path_list'));
            }

            return $this->sendResponse([...$data, 'list' => ChallengePathResource::collection(data_get($data, 'list'))], __('responses.advance_search.challenge_path_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_challenge_path_list'));
        }
    }

    public function searchChallengeTemplates(ChallengeTemplateSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchChallengeTemplates($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_challenge_template_list'));
            }

            return $this->sendResponse([...$data, 'list' => ChallengeTemplateResource::collection(data_get($data, 'list'))], __('responses.advance_search.challenge_template_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_challenge_template_list'));
        }
    }

    public function searchResourceModules(ResourceModuleSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchResourceModules($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_resource_module_list'));
            }

            return $this->sendResponse([...$data, 'list' => ResourceModuleResource::collection(data_get($data, 'list'))], __('responses.advance_search.resource_module_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_resource_module_list'));
        }
    }

    public function searchResourceGroups(ResourceGroupSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchResourceGroups($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_resource_group_list'));
            }

            return $this->sendResponse([...$data, 'list' => ResourceGroupResource::collection(data_get($data, 'list'))], __('responses.advance_search.resource_group_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_resource_group_list'));
        }
    }

    public function searchResourceCollections(ResourceCollectionSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchResourceCollections($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_resource_collection_list'));
            }

            return $this->sendResponse([...$data, 'list' => ResourceCollectionResource::collection(data_get($data, 'list'))], __('responses.advance_search.resource_collection_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_resource_collection_list'));
        }
    }

    public function searchProjects(ProjectSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchProjects($request->validated('keyword'), $request->formattedFilter());
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_project_list'));
            }

            return $this->sendResponse([
                ...$data,
                'list' => ProjectResource::collection(data_get($data, 'list')),
            ], __('responses.advance_search.project_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_project_list'));
        }
    }

    public function searchOrganization(OrganizationSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchOrganization($request->validated('keyword'));
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_organization_lis'));
            }

            return $this->sendResponse([
                ...$data,
                'list' => OrganizationResource::collection(data_get($data, 'list')),
            ], __('responses.advance_search.organization_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_organization_lis'));
        }
    }

    public function searchUsers(UserSearchRequest $request)
    {
        try {
            $data = $this->advanceSearchRepository->searchUsers($request->validated('keyword'));
            if ($data === false) {
                return $this->sendError(__('responses.advance_search.failed_to_fetch_user_list'));
            }

            return $this->sendResponse([
                ...$data,
                'list' => UserResource::collection(data_get($data, 'list')),
            ], __('responses.advance_search.user_lists'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.advance_search.failed_to_fetch_user_list'));
        }
    }
}
