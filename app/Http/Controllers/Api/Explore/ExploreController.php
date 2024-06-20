<?php

namespace App\Http\Controllers\Api\Explore;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Explore\SkillResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Repositories\Api\Explore\ExploreRepository;
use App\Services\UserSkillsService;

class ExploreController extends AppBaseController
{
    private $exploreRepository;

    public function __construct(ExploreRepository $exploreRepository)
    {
        $this->exploreRepository = $exploreRepository;
    }

    public function index($action)
    {
        try {
            if (!in_array($action, ['recommended', 'featured', 'teams'])) {
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
            } else {
                $getTendingJobs = $this->exploreRepository->trendingJobs();
                if ($getTendingJobs) {
                    $response = [
                        'labs'        => LabResource::collection($getTendingJobs['labs']),
                        'challenges'  => ChallengeResource::collection($getTendingJobs['challenge']),
                    ];

                    return $this->sendResponse($response, __('responses.trending_labs_challenges_successfully'));
                }

                return $this->sendResponse([], __('responses.trending_labs_challenges_successfully'));
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
