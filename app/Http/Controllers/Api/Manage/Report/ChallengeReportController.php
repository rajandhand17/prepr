<?php

namespace App\Http\Controllers\Api\Manage\Report;

use App\Exports\Challenge\ChallengeExport;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Report\ChallengeAchievementResource;
use App\Http\Resources\Manage\Report\ChallengeAssessmentDetailResource;
use App\Http\Resources\Manage\Report\ChallengeAssessmentResource;
use App\Http\Resources\Manage\Report\ChallengeMemberResource;
use App\Http\Resources\Manage\Report\Components\ChallengeReportDetailsResource;
use App\Http\Resources\Manage\Report\Components\LabProgramResource;
use App\Http\Resources\Manage\Report\Components\LabResource;
use App\Http\Resources\Manage\Report\Components\ResourceCollectionResource;
use App\Http\Resources\Manage\Report\Components\ResourceGroupResource;
use App\Http\Resources\Manage\Report\Components\ResourceModuleResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use App\Repositories\Api\Manage\Report\Challenge\ChallengeReportRepository;
use App\Repositories\Api\Project\ProjectRepository;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ChallengeReportController extends AppBaseController
{
    public function __construct(
        protected ChallengeReportRepository $challengeReportRepository,
        protected ChallengeRepository $challengeRepository,
        protected ProjectRepository $projectRepository
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
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);

                if (!$organization) {
                    return $this->sendError(__('responses.selected_organization_not_found'), 404);
                }
                if ($challenge->organization_id != $organization->id) {
                    return $this->sendError(__('responses.challenge_switcher_error'), 403);
                }
                if ($challenge->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_not_accessible'), 403);
                }

                /**
                 * LAZY LOADING COUNTS.
                 */
                $challenge->loadCount([
                    'members',
                    'hosts',
                    'submitted_projects',
                    'labs',
                    'labPrograms',
                    'resourceModules',
                    'resourceCollections',
                    'resourceGroups',
                ]);

                return $this->sendResponse(ChallengeReportDetailsResource::make($challenge), __('Challenge details.'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_details'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeMemberProgress(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $memberProgress = $this->challengeReportRepository->getChallengeMemberProgress($challenge);

                if ($memberProgress !== false) {
                    return $this->sendResponse($memberProgress, __('Resource module member progress'));
                }

                return $this->sendError(__('responses.failed_to_fetch_challenge_member_progress'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_resource_module_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_member_progress'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeLabs(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedLabs($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => LabResource::collection(data_get($data, 'list')),
                    ], __('Lab list'));
                }

                return $this->sendError(__('responses.failed_to_fetch_challenge_labs'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_challenge_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_labs'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeLabPrograms(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedLabPrograms($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => LabProgramResource::collection(data_get($data, 'list')),
                    ], __('Lab program list'));
                }

                return $this->sendError(__('responses.failed_to_fetch_challenge_lab_programs'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_challenge_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_lab_programs'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeResourceModules(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedResourceModules($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ResourceModuleResource::collection(data_get($data, 'list')),
                    ], __('Resource Module list'));
                }

                return $this->sendError(__('responses.failed_to_fetch_challenge_resource_modules'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_challenge_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_resource_modules'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeResourceCollection(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedResourceCollections($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ResourceCollectionResource::collection(data_get($data, 'list')),
                    ], __('Resource collection list'));
                }

                return $this->sendError(__('responses.failed_to_fetch_challenge_resource_collections'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_challenge_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_resource_collections'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeResourceGroups(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedResourceGroups($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ResourceGroupResource::collection(data_get($data, 'list')),
                    ], __('Resource group'));
                }

                return $this->sendError(__('responses.failed_to_fetch_challenge_resource_groups'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_challenge_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_resource_groups'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeEngagement(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getChallengeEngagement($challenge);

                if ($data === false) {
                    return $this->sendError(__('responses.failed_to_fetch_challenge_engagement'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendResponse($data, __('Challenge engagements.'));
            }

            return $this->sendError(__('responses.not_found_challenge_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_engagement'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function challengeAchievements(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedAchievements($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ChallengeAchievementResource::collection(data_get($data, 'list')),
                    ], __('Challenge Achievements list'));
                }

                return $this->sendResponse($data, __('Challenge Achievements.'));
            }

            return $this->sendError(__('responses.not_found_challenge_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_achievements'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function challengeMembers(string $slug): JsonResponse
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedMembers($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ChallengeMemberResource::collection(data_get($data, 'list')),
                    ], __('Challenge Members list'));
                }

                return $this->sendResponse($data, __('Challenge Members.'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_members'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function challengeAssessments(string $slug): JsonResponse
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getPaginatedAssessments($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'list' => ChallengeAssessmentResource::collection(data_get($data, 'list')),
                    ], __('Challenge Assessment'));
                }

                return $this->sendResponse($data, __('Challenge Assessment.'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_assessments'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     * @param string $project_slug
     *
     * @return JsonResponse
     */
    public function challengeAssessmentDetail(string $slug, string $project_slug): JsonResponse
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            $project = $this->projectRepository->getProjectBasedOnSlug($project_slug);

            if (!$project) {
                return $this->sendError(__('responses.project_slug_not_found'), Response::HTTP_NOT_FOUND);
            }

            if ($challenge) {
                $data = $this->challengeReportRepository->getChallengeAssessmentDetail($challenge, $project->id);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                        'users' => ChallengeAssessmentDetailResource::collection(data_get($data, 'users')),
                    ], __('Challenge Assessment'));
                }

                return $this->sendResponse($data, __('Challenge Assessment.'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_assessment_details'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function challengeAssociatedProjects(string $slug): JsonResponse
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                $data = $this->challengeReportRepository->getChallengeAssociatedProjects($challenge);

                if ($data !== false) {
                    return $this->sendResponse([
                        ...$data,
                    ], __('Challenge Assessment'));
                }

                return $this->sendResponse($data, __('Challenge Assessment.'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_associated_projects'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     */
    public function challengeMemberActivity(string $slug)
    {
        try {
            $lab = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if ($lab) {
                $data = $this->challengeReportRepository->challengeMemberActivity($lab);
                if ($data === false) {
                    return $this->sendError(__('Failed to fetch challenge member activity.'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendResponse($data, __('Challenge member progress.'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_challenge_member_activity'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return BinaryFileResponse
     */
    public function challengeExport(string $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);

            if ($challenge) {
                return Excel::download(new ChallengeExport($challenge), sprintf('%s-challenge-excel.xlsx', $challenge->slug));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), 404);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_export_challenge_details'), Response::HTTP_BAD_REQUEST);
        }
    }
}
