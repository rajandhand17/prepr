<?php

namespace App\Http\Controllers\Api\Manage\Report;

use App\Exports\Lab\LabExport;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Report\Components\ChallengePathResource;
use App\Http\Resources\Manage\Report\Components\ChallengeResource;
use App\Http\Resources\Manage\Report\Components\ResourceCollectionResource;
use App\Http\Resources\Manage\Report\Components\ResourceGroupResource;
use App\Http\Resources\Manage\Report\Components\ResourceModuleResource;
use App\Http\Resources\Manage\Report\LabMemberResource;
use App\Http\Resources\Manage\Report\LabReportDetailsResource;
use App\Http\Resources\Public\Lab\LabAchievementResource;
use App\Repositories\Api\Manage\Lab\LabRepository;
use App\Repositories\Api\Manage\Report\Lab\LabReportRepository;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LabReportController extends AppBaseController
{
    public function __construct(
        protected LabReportRepository $labReportRepository,
        protected LabRepository $labRepository
    ) {
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function details(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                if ($lab->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_not_accessible'), 403);
                }

                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);

                if (!$organization) {
                    return $this->sendError(__('responses.selected_organization_not_found'), 404);
                }

                if ($lab->organization_id != $organization->id) {
                    return $this->sendError(__('responses.lab_switcher_error'), 403);
                }

                /**
                 * LAZY LOADING COUNTS.
                 */
                $lab->loadCount([
                    'members',
                    'challenges',
                    'challengePaths',
                    'resourceModules',
                    'resourceCollections',
                    'resourceGroups',
                ]);

                return $this->sendResponse(LabReportDetailsResource::make($lab), __('Lab details.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_details'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function labEngagement(string $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                $data = $this->labReportRepository->getLabEngagement($lab);
                if ($data === false) {
                    return $this->sendError(__('responses.failed_to_fetch_lab_details'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendResponse($data, __('Lab engagements'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_details'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function labMemberProgress(string $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                $data = $this->labReportRepository->labMemberProgress($lab);
                if ($data === false) {
                    return $this->sendError(__('Failed to fetch lab member progress.'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendResponse($data, __('Lab member progress.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('Failed to fetch lab member progress.'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     */
    public function labMemberActivity(string $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                $data = $this->labReportRepository->labMemberActivity($lab);
                if ($data === false) {
                    return $this->sendError(__('responses.failed_to_fetch_lab_member_activity'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendResponse($data, __('Lab member progress.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_member_activity'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function labChallenges(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                $data = $this->labReportRepository->getPaginatedChallenges($lab);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ChallengeResource::collection(data_get($data, 'list')),
                    ], __('Challenge list'));
                }

                return $this->sendResponse($data, __('Lab challenges'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_challenges'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function labResourceModules(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                $data = $this->labReportRepository->getPaginatedResourceModules($lab);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ResourceModuleResource::collection(data_get($data, 'list')),
                    ], __('Lab Resource Modules list'));
                }

                return $this->sendResponse($data, __('Lab Resource Modules.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_resource_modules'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function labResourceCollections(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                $data = $this->labReportRepository->getPaginatedResourceCollections($lab);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ResourceCollectionResource::collection(data_get($data, 'list')),
                    ], __('Lab Resource Collections list'));
                }

                return $this->sendResponse($data, __('Lab Resource Collections.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_resource_collections'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function labResourceGroups(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                $data = $this->labReportRepository->getPaginatedResourceGroups($lab);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ResourceGroupResource::collection(data_get($data, 'list')),
                    ], __('Lab Resource Groups list'));
                }

                return $this->sendResponse($data, __('Lab Resource Groups.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_resource_groups'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function labChallengePaths(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                $data = $this->labReportRepository->getPaginatedChallengePaths($lab);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ChallengePathResource::collection(data_get($data, 'list')),
                    ], __('Lab Challenge Paths list'));
                }

                return $this->sendResponse($data, __('Lab Challenge Paths.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_challenge_paths'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function labAchievements(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                $data = $this->labReportRepository->getPaginatedAchievements($lab);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => LabAchievementResource::collection(data_get($data, 'list')),
                    ], __('Lab Achievements list'));
                }

                return $this->sendResponse($data, __('Lab Achievements.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_achievements'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function labMembers(string $slug): JsonResponse
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                $data = $this->labReportRepository->getPaginatedMembers($lab);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => LabMemberResource::collection(data_get($data, 'list')),
                    ], __('Lab Members list'));
                }

                return $this->sendResponse($data, __('Lab Members.'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_members'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return BinaryFileResponse
     */
    public function labExport(string $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                return Excel::download(new LabExport($lab), sprintf('%s-lab-excel.xlsx', $lab->slug));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_export_lab_details'), Response::HTTP_BAD_REQUEST);
        }
    }
}
