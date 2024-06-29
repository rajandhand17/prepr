<?php

namespace App\Http\Controllers\Api\Explore;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Explore\SkillResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Http\Resources\TeamMatching\TeamMatchingListResource;
use App\Repositories\Api\Explore\ExploreRepository;
use App\Repositories\Api\TeamMatching\TeamMatchingRepository;
use App\Services\UserSkillsService;
use Illuminate\Http\Request;

class ExploreController extends AppBaseController
{
    private $exploreRepository;
    private $teamMatchingRepository;

    public function __construct(ExploreRepository $exploreRepository, TeamMatchingRepository $teamMatchingRepository)
    {
        $this->exploreRepository = $exploreRepository;
        $this->teamMatchingRepository = $teamMatchingRepository;
    }

    public function index($action)
    {
        try {
            if (!in_array($action, ['recommended', 'featured'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $response = [];
            switch ($action) {
                case 'recommended':
                    $explore = $this->exploreRepository->recommendedLabsAndChallenges();
                    if ($explore) {
                        $response = [
                            'labs'        => LabResource::collection($explore['labs']),
                            'challenges'  => ChallengeResource::collection($explore['challenge']),
                        ];
                        $message = __('responses.recommended_labs_challenges_successfully');
                    } else {
                        $message = __('responses.recommended_labs_challenges_failed');
                    }
                    break;
                case 'featured':
                    $featured = $this->exploreRepository->getFeaturedLabs();
                    if ($featured) {
                        $response = LabResource::collection($featured);
                        $message = __('responses.featured_labs_successfully');
                    } else {
                        $message = __('responses.featured_labs_failed');
                    }
                    break;
                case 'teams':
                    $featured = $this->exploreRepository->getFeaturedLabs();
                    if ($featured) {
                        $response = LabResource::collection($featured);
                        $message = __('responses.featured_labs_successfully');
                    } else {
                        $message = __('responses.featured_labs_failed');
                    }
                    break;
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                    break;
            }
            if ($response) {
                return $this->sendResponse($response, $message);
            }

            return $this->sendError(__('responses.send_error'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function recommendedOrTrendingLabAndChallenge()
    {
        try {
            $getUserSkills = UserSkillsService::getUserSkills();
            if (isset($getUserSkills) && count($getUserSkills)) {
                $recommendedSkills = $this->exploreRepository->recommendedSkills($getUserSkills);
                if (!empty($recommendedSkills)) {
                    return $this->sendResponse(SkillResource::collection($recommendedSkills), __('responses.recommended_skills_successfully'));
                }

                return $this->sendResponse([], __('responses.recommended_skills_successfully'));
            }

            return $this->sendResponse([], __('responses.recommended_skills_successfully'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $getProjectIds = $this->teamMatchingRepository->getMatchingTeams();
            if (count($getProjectIds) < 6) {
                $projectIds = $this->teamMatchingRepository->getBrowsersList(auth()->user());
                $getProjectIds->merge($projectIds);
            }
            if ($getProjectIds) {
                $project = $this->teamMatchingRepository->getProjectList($getProjectIds, $request)->take(6);

                return $this->sendResponse(TeamMatchingListResource::collection($project), __('responses.teams_list'));
            }

            // return $this->sendResponse($response, __('responses.team_matching_list_successfully'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
