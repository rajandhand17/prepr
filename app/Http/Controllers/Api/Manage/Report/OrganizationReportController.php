<?php

namespace App\Http\Controllers\Api\Manage\Report;

use App\Exports\Organization\OrganizationExport;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Report\Components\ChallengePathResource;
use App\Http\Resources\Manage\Report\Components\ChallengeResource;
use App\Http\Resources\Manage\Report\Components\LabProgramResource;
use App\Http\Resources\Manage\Report\Components\LabResource;
use App\Http\Resources\Manage\Report\Components\ResourceCollectionResource;
use App\Http\Resources\Manage\Report\Components\ResourceGroupResource;
use App\Http\Resources\Manage\Report\Components\ResourceModuleResource;
use App\Http\Resources\Manage\Report\OrganizationMemberResource;
use App\Http\Resources\Manage\Report\OrganizationReportDetailResource;
use App\Repositories\Api\Manage\Organization\OrganizationRepository;
use App\Repositories\Api\Manage\Report\Organization\OrganizationReportRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class OrganizationReportController extends AppBaseController
{
    public function __construct(
        protected OrganizationRepository $organizationRepository,
        protected OrganizationReportRepository $organizationReportRepository
    ) {
    }

    public function details($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            /**
             * LAZY LOADING COUNTS.
             */
            $organization->loadCount([
                'members',
                'challenges_count',
                'labs_count',
                'resource_modules_count',
                'challenge_paths_count',
                'lab_programs_count',
                'resource_collections_count',
                'resource_groups_count',
            ]);

            return $this->sendResponse(OrganizationReportDetailResource::make($organization), __('organization report details.'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_report_details'), 400);
        }
    }

    public function organizationEngagement($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);

            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }

            $data = $this->organizationReportRepository->getOrganizationEngagements($organization);

            return $this->sendResponse($data, __('Organization Engagement Details'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_engagement'), 400);
        }
    }

    public function organizationMembers($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getOrganizationMembers($organization);

            return $this->sendResponse([
                ...$data,
                'list' => OrganizationMemberResource::collection(data_get($data, 'list')),
            ], __('organization members list.'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_members'), 400);
        }
    }

    public function organizationChallenges($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getPaginatedChallenges($organization);
            if ($data !== false) {
                return $this->sendResponse([
                    ...$data,
                    'list' => ChallengeResource::collection(data_get($data, 'list')),
                ], __('Challenge list'));
            }

            return $this->sendError(__('responses.failed_to_fetch_organization_challenges'), 400);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_challenges'), 400);
        }
    }

    public function organizationChallengePath($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getPaginatedChallengePath($organization);

            if ($data !== false) {
                return $this->sendResponse([
                    ...$data,
                    'list' => ChallengePathResource::collection(data_get($data, 'list')), //todo: change resource
                ], __('Challenge Path list'));
            }

            return $this->sendError(__('responses.failed_to_fetch_organization_challenge_paths'), 400);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_challenge_paths'), 400);
        }
    }

    public function organizationResourceModules($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getPaginatedResourceModule($organization);
            if ($data !== false) {
                return $this->sendResponse([
                    ...$data,
                    'list' => ResourceModuleResource::collection(data_get($data, 'list')),
                ], __('resource module list'));
            }

            return $this->sendError(__('responses.failed_to_fetch_organization_resource_modules'), 400);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_resource_modules'), 400);
        }
    }

    public function organizationLabs($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getPaginatedLabs($organization);
            if ($data !== false) {
                return $this->sendResponse([
                    ...$data,
                    'list' => LabResource::collection(data_get($data, 'list')),
                ], __('lab list'));
            }

            return $this->sendError(__('responses.failed_to_fetch_organization_labs'), 400);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_labs'), 400);
        }
    }

    public function organizationLabPrograms($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getPaginatedLabPrograms($organization);
            if ($data !== false) {
                return $this->sendResponse([
                    ...$data,
                    'list' => LabProgramResource::collection(data_get($data, 'list')),
                ], __('lab program list'));
            }

            return $this->sendError(__('responses.failed_to_fetch_organization_lab_programs'), 400);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_lab_programs'), 400);
        }
    }

    public function organizationResourceCollections($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getPaginatedResourceCollection($organization);
            if ($data !== false) {
                return $this->sendResponse([
                    ...$data,
                    'list' => ResourceCollectionResource::collection(data_get($data, 'list')),
                ], __('Resource Collection list'));
            }

            return $this->sendError(__('responses.failed_to_fetch_organization_resource_collections'), 400);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_resource_collections'), 400);
        }
    }

    public function organizationResourceGroups($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_exists'), 404);
            }
            $data = $this->organizationReportRepository->getPaginatedResourceGroup($organization);
            if ($data !== false) {
                return $this->sendResponse([
                    ...$data,
                    'list' => ResourceGroupResource::collection(data_get($data, 'list')),
                ], __('Resource group list'));
            }

            return $this->sendError(__('responses.failed_to_fetch_organization_resource_groups'), 400);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_resource_groups'), 400);
        }
    }

    /**
     * @param string $slug
     */
    public function organizationMemberActivity(string $slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if ($organization) {
                $data = $this->organizationReportRepository->getOrganizationMemberActivity($organization);
                if ($data === false) {
                    return $this->sendError(__('responses.failed_to_fetch_organization_member_activity'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendResponse($data, __('organization member progress.'));
            }

            return $this->sendError(__('responses.organization_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_organization_member_activity'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return BinaryFileResponse
     */
    public function organizationExport(string $slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);

            if ($organization) {
                $download = Excel::download(new OrganizationExport($organization),
                    sprintf('%s-organization-excel.xlsx', $organization->slug));
                $filename = sprintf('organization-report/%s-organization-excel.xlsx', $organization->slug);
                Storage::disk('s3')->put($filename, $download);
                return redirect(Storage::temporaryUrl($filename,Carbon::now()->addMinutes(30)));
            }

            return $this->sendError(__('responses.organization_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_export_organization_details'), Response::HTTP_BAD_REQUEST);
        }
    }
}
