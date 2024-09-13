<?php

namespace App\Http\Controllers\Api\Master;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Master\CreateSponsorRequest;
use App\Http\Resources\Master\AcheivementConditionListResource;
use App\Http\Resources\Master\BusinessChallengeTacklingResource;
use App\Http\Resources\Master\CategoryResource;
use App\Http\Resources\Master\ChallengeAnnouncementRecipientResource;
use App\Http\Resources\Master\ChallengePitchTasksResource;
use App\Http\Resources\Master\CountryResource;
use App\Http\Resources\Master\DurationsResource;
use App\Http\Resources\Master\FlexibleDateDurationResource;
use App\Http\Resources\Master\HostResource;
use App\Http\Resources\Master\JobTitleResource;
use App\Http\Resources\Master\LabConditionResource;
use App\Http\Resources\Master\LevelsResource;
use App\Http\Resources\Master\ProjectIndustryResource;
use App\Http\Resources\Master\ProjectStageResource;
use App\Http\Resources\Master\ProjectStatusResource;
use App\Http\Resources\Master\ProjectSubmissionRequirementResource;
use App\Http\Resources\Master\ProjectTypeResource;
use App\Http\Resources\Master\ProjectVerticalsResource;
use App\Http\Resources\Master\RankResource;
use App\Http\Resources\Master\SkillGroupResource;
use App\Http\Resources\Master\SkillResource;
use App\Http\Resources\Master\SkillStackResource;
use App\Http\Resources\Master\SocialConnect;
use App\Http\Resources\Master\SocialLinkResource;
use App\Http\Resources\Master\TagGroupResource;
use App\Http\Resources\Master\TagResource;
use App\Repositories\Api\Master\MasterRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterController extends AppBaseController
{
    private $masterRepository;

    public function __construct(MasterRepository $masterRepository)
    {
        $this->masterRepository = $masterRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/categories",
     *     tags={"Master API - Categories"},
     *     summary="Finds lists of categories",
     *     description="Get all the categories lists",
     *     operationId="getCategories",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error!",
     *
     *     ),
     * )
     */
    public function getCategories(Request $request)
    {
        try {
            $category = $this->masterRepository->getCategories($request);
            if ($category) {
                return $this->sendResponse(CategoryResource::collection($category), __('responses.found_category_list'));
            }

            return $this->sendError(__('responses.not_found_category_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/skills",
     *     tags={"Master API -Skills"},
     *     summary="Finds lists of skills",
     *     description="Get all the skills lists",
     *     operationId="getSkills",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getSkills(Request $request)
    {
        try {
            $skills = $this->masterRepository->getSkills($request);
            if ($skills) {
                return $this->sendResponse(SkillResource::collection($skills), __('responses.found_skill_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_skill_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/tags",
     *     tags={"Master API -Tags"},
     *     summary="Finds lists of tags",
     *     description="Get all tags lists",
     *     operationId="getTags",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *
     *     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getTags(Request $request)
    {
        try {
            $tag = $this->masterRepository->getTags($request);
            if ($tag) {
                return $this->sendResponse(TagResource::collection($tag), __('responses.found_tag_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_tag_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/industries",
     *     tags={"Master API - Project Industries"},
     *     summary="Finds lists of tags",
     *     description="Get all tags lists",
     *     operationId="getProjectIndustries",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getProjectIndustries(Request $request)
    {
        try {
            $industry = $this->masterRepository->getProjectIndustries($request);
            if ($industry) {
                return $this->sendResponse(ProjectIndustryResource::collection($industry), __('responses.found_project_industry_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_project_industry_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/types",
     *     tags={"Master API - Project Types"},
     *     summary="Finds lists of tags",
     *     description="Get all tags lists",
     *     operationId="getProjectTypes",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getProjectTypes(Request $request)
    {
        try {
            $type = $this->masterRepository->getProjectTypes($request);
            if ($type) {
                return $this->sendResponse(ProjectTypeResource::collection($type), __('responses.found_project_type_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_project_type_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/stages",
     *     tags={"Master API - Project Stages"},
     *     summary="Finds lists of Stages",
     *     description="Get all tags Stages",
     *     operationId="getProjectStages",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getProjectStages(Request $request)
    {
        try {
            $stages = $this->masterRepository->getStages($request);
            if ($stages) {
                return $this->sendResponse(ProjectStageResource::collection($stages), __('responses.found_project_stages_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_project_stages_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/verticals",
     *     tags={"Master API - Project Verticals"},
     *     summary="Finds lists of project verticals",
     *     description="Get all tags project verticals",
     *     operationId="getProjectVerticals",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getProjectVerticals(Request $request)
    {
        try {
            $project_verticals = $this->masterRepository->getVerticals($request);
            if ($project_verticals) {
                return $this->sendResponse(ProjectVerticalsResource::collection($project_verticals), __('responses.found_project_verticals_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_project_verticals_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/status",
     *     tags={"Master API - Project Status"},
     *     summary="Finds lists of project status",
     *     description="Get all tags project status",
     *     operationId="getProjectStatus",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getProjectStatus(Request $request)
    {
        try {
            $project_status = $this->masterRepository->getStatus($request);
            if ($project_status) {
                return $this->sendResponse(ProjectStatusResource::collection($project_status), __('responses.found_project_status_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_project_status_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/links",
     *     tags={"Master API - Project Links"},
     *     summary="Finds lists of project status",
     *     description="Get all tags project status",
     *     operationId="getSocialLinks",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getSocialLinks(Request $request)
    {
        try {
            $status = $this->masterRepository->getSocialLinks($request);
            if ($status) {
                return $this->sendResponse(SocialLinkResource::collection($status), __('responses.found_social_links_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_social_links_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/skill-groups",
     *     tags={"Master API - Skill Group"},
     *     summary="Finds lists of skill group",
     *     description="Get all tags skill group",
     *     operationId="getSkillGroups",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="skills",
     *         in="query",
     *         description="skills values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="skill_stacks",
     *         in="query",
     *         description="skill stacks values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getSkillGroups(Request $request)
    {
        try {
            $get_skill_groups = $this->masterRepository->getSkillGroups($request);
            if ($get_skill_groups) {
                return $this->sendResponse(SkillGroupResource::collection($get_skill_groups), __('responses.found_skill_groups_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_skill_groups_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/skill-sets",
     *     tags={"Master API - Skill Sets"},
     *     summary="Finds lists of skill Set",
     *     description="Get all tags skill Sets",
     *     operationId="getSkillStacks",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getSkillStacks(Request $request)
    {
        try {
            $get_skill_stacks = $this->masterRepository->getSkillStacks($request);
            if ($get_skill_stacks) {
                return $this->sendResponse(SkillStackResource::collection($get_skill_stacks), __('responses.found_skill_stacks_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_skill_stacks_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/ranks",
     *     tags={"Master API - Ranks"},
     *     summary="Finds lists of ranks",
     *     description="Get all tags ranks",
     *     operationId="getRanks",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getRanks(Request $request)
    {
        try {
            $get_ranks = $this->masterRepository->getRanks($request);
            if ($get_ranks) {
                return $this->sendResponse(RankResource::collection($get_ranks), __('responses.found_rank_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_rank_list'));
        } catch (\Exception) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/project-submission-requirement",
     *     tags={"Master API - Project Submission Requirements"},
     *     summary="Finds lists of project submission requirement",
     *     description="Get all tags project submission requirement",
     *     operationId="getProjectSubmissionRequirements",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getProjectSubmissionRequirements(Request $request)
    {
        try {
            $project_submission_requirements = $this->masterRepository->getProjectSubmissionRequirements($request);
            if ($project_submission_requirements) {
                return $this->sendResponse(ProjectSubmissionRequirementResource::collection($project_submission_requirements), __('responses.found_project_submission_requirements_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_project_submission_requirements_list'));
        } catch (\Exception) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/achievement-condition-list",
     *     tags={"Master API - achievement Condition List"},
     *     summary="Finds lists of project achievement condition list",
     *     description="Get all list of project achievement condition list",
     *     operationId="getAchievementConditionLists",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getAchievementConditionLists(Request $request)
    {
        try {
            $acheivement_condition_list = $this->masterRepository->getAchievementConditionLists($request);
            if ($acheivement_condition_list) {
                return $this->sendResponse(AcheivementConditionListResource::collection($acheivement_condition_list), __('responses.found_achievement_condition_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_achievement_condition_list'));
        } catch (\Exception) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/host",
     *     tags={"Master API - Host"},
     *     summary="Finds lists of Host",
     *     description="Get all list of Host",
     *     operationId="getHosts",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getHosts(Request $request)
    {
        try {
            $host_list = $this->masterRepository->getHosts($request);
            if ($host_list) {
                return $this->sendResponse(HostResource::collection($host_list), __('responses.found_host_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_host_list'));
        } catch (\Exception) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/flexible-date-duration",
     *     tags={"Master API - Flexible Date Duration"},
     *     summary="Finds lists of flexible date duration",
     *     description="Get all list of flexible date duration",
     *     operationId="getFlexibleDateDurations",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getFlexibleDateDurations(Request $request)
    {
        try {
            $flexible_date_duration = $this->masterRepository->getFlexibleDateDurations($request);
            if ($flexible_date_duration) {
                return $this->sendResponse(FlexibleDateDurationResource::collection($flexible_date_duration), __('responses.found_flexible_list'));
            }

            return $this->sendResponse(null, __('responses.found_flexible_list'));
        } catch (\Exception) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/pitch-templates",
     *     tags={"Master API - Pitch Templates"},
     *     summary="Finds lists of pitch templates",
     *     description="Get all list of pitch templates",
     *     operationId="getPitchTemplates",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getPitchTemplates(Request $request)
    {
        try {
            $pitch_templates = $this->masterRepository->getPitchTemplates($request);
            if ($pitch_templates) {
                return $this->sendResponse(FlexibleDateDurationResource::collection($pitch_templates), __('responses.found_pitch_templates_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_pitch_templates_list'));
        } catch (\Exception) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/lab-conditions",
     *     tags={"Master API - Lab Conditions"},
     *     summary="Finds lists of lab conditions",
     *     description="Get all list of lab conditions",
     *     operationId="getLabConditions",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getLabConditions(Request $request)
    {
        try {
            $lab_condition = $this->masterRepository->getLabConditions($request);
            if ($lab_condition) {
                return $this->sendResponse(LabConditionResource::collection($lab_condition), __('responses.found_lab_condition_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_lab_condition_list'));
        } catch (\Exception) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/social-connect",
     *     tags={"Master API - Social Connect"},
     *     summary="Finds lists of social connect",
     *     description="Get all social connect list",
     *     operationId="getSocialConnect",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getSocialConnect(Request $request)
    {
        try {
            $socialconnect = $this->masterRepository->getSocialConnect($request);
            if ($socialconnect) {
                return $this->sendResponse(SocialConnect::collection($socialconnect), __('responses.social_connect_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_social_connect_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getDurations(Request $request)
    {
        try {
            $durations = $this->masterRepository->getDurations($request);
            if ($durations) {
                return $this->sendResponse(DurationsResource::collection($durations), __('responses.found_duration'));
            }

            return $this->sendResponse(null, __('responses.not_found_duration'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getLevels(Request $request)
    {
        try {
            $levels = $this->masterRepository->getLevels($request);
            if ($levels) {
                return $this->sendResponse(LevelsResource::collection($levels), __('responses.found_levels'));
            }

            return $this->sendResponse(null, __('responses.not_found_levels'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getChallengePitchTask(Request $request)
    {
        try {
            $templatePitchTaskData = $this->masterRepository->getPitchTaskData($request);
            if ($templatePitchTaskData) {
                return $this->sendResponse(ChallengePitchTasksResource::collection($templatePitchTaskData), __('responses.challenge_pitch_task_available'));
            }

            return $this->sendResponse(null, __('responses.challenge_pitch_task_not_available'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function createSponsor(CreateSponsorRequest $request)
    {
        try {
            $checkSponsor = $this->masterRepository->checkSponsor($request);
            if ($checkSponsor) {
                return $this->sendResponse([], __('responses.sponsor_host_not_available'), 403);
            }

            $upload_sponsor_image = config('site-settings.default_user_profile_image');
            if ($request->image !== null) {
                $uploaded_sponsor_image = $this->masterRepository->uploadSponsorMedia($request->image);
                if (!$uploaded_sponsor_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_sponsor_image = $uploaded_sponsor_image;
            }

            $createSponsorHost = $this->masterRepository->createSponsor($request, $upload_sponsor_image);
            if ($createSponsorHost) {
                return $this->sendResponse($createSponsorHost, __('responses.sponsor_host_added'), 200);
            }

            return $this->sendError(__('responses.sponsor_host_stored_failed'), 403);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getChallengeAnnouncementRecipient(Request $request)
    {
        try {
            $levels = $this->masterRepository->getChallengeAnnouncementRecipient($request);
            if ($levels) {
                return $this->sendResponse(ChallengeAnnouncementRecipientResource::collection($levels), __('responses.found_challenge_announcement_recipient'));
            }

            return $this->sendResponse(null, __('responses.not_found_challenge_announcement_recipient'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getTagGroup(Request $request)
    {
        try {
            $get_tag_groups = $this->masterRepository->getTagGroups($request);
            if ($get_tag_groups) {
                return $this->sendResponse(TagGroupResource::collection($get_tag_groups), __('responses.found_tag_groups_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_tag_groups_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getCountries(Request $request)
    {
        try {
            $getCountryList = $this->masterRepository->getCountries($request);
            if ($getCountryList->isNotEmpty()) {
                return $this->sendResponse(CountryResource::collection($getCountryList), __('responses.country_list_fetched_successfully'));
            }

            return $this->sendError(__('responses.countries_fetched_failed'), 404);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/master/job-titles",
     *     tags={"Master API -JobTitles"},
     *     summary="Finds lists of job titles",
     *     description="Get all the job titles lists",
     *     operationId="getJobTitles",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *
     *     ),
     * )
     */
    public function getJobTitles(Request $request)
    {
        try {
            $jobTitles = $this->masterRepository->getJobTitles($request);
            if ($jobTitles) {
                return $this->sendResponse(JobTitleResource::collection($jobTitles), __('responses.found_job_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_job_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in getJobTitles in MasterController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }

    public function businessChallengeTackling(Request $request)
    {
        try {
            $businessChallengeTackling = $this->masterRepository->getBusinessChallengeTackling($request);
            if ($businessChallengeTackling) {
                return $this->sendResponse(BusinessChallengeTacklingResource::collection($businessChallengeTackling), __('responses.found_bct_list'));
            }

            return $this->sendResponse(null, __('responses.not_found_bct_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
