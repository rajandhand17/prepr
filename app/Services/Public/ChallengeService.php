<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengePitch;
use App\Models\ChallengeTask;
use App\Models\MemberManagement;
use App\Models\PitchTemplate;
use App\Models\Project;
use App\Services\Manage\ChallengeSkillsGroupsStackService;
use App\Services\ProjectService;
use App\Services\ProjectSubmissionRequirementService;
use Carbon\Carbon;
use Exception;

class ChallengeService
{
    public function getList($request)
    {
        try {
            $challenge_list = Challenge::where('challenges.status', '1')->where('challenges.is_accessible', '1');
            $challenge_list = self::filterChallengeList($request, $challenge_list);

            return $challenge_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeList($challengeIds)
    {
        try {
            $challenge_list = Challenge::whereIn('challenges.id', $challengeIds)->where(['challenges.status' => '1', 'challenges.is_accessible' => '1']);

            return $challenge_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function filterChallengeList($request, $challenge_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $challenge_list = $challenge_list->whereSearchFilter($request->search ?? '');
            }

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'deactivated') ? '2' : '3'));
                $challenge_list = $challenge_list->where('challenges.status', $status);
            } else {
                $challenge_list = $challenge_list->where('challenges.status', '1');
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $challenge_list = $challenge_list->whereIn('challenges.category_id', $request->category);
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                $challenge_list = $challenge_list->whereIn('organization_id', $getOrganizationIds);
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $challengeIds = ChallengeSocialActivitiesService::getChallengeBasedOnActivity($activityType)->pluck('challenge_id');
                $challenge_list->whereIn('challenges.id', $challengeIds);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $challenge_list->orderBy('challenges.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $challenge_list->orderBy('challenges.title', 'DESC');
                        break;
                    case 'creation_date':
                        $challenge_list->orderBy('challenges.created_at', 'ASC');
                        break;
                    default:
                        $challenge_list->orderBy('challenges.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $challenge_list = $challenge_list->where('challenges.privacy', '0');
                        break;
                    case 'private':
                        $challenge_list = $challenge_list->where('challenges.privacy', '1');
                        break;
                }
            }

            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $challenge_list = $challenge_list->whereIn('challenges.id', function ($query) use ($request) {
                    $query->select('challenge_skills_groups_stacks.challenge_id')
                        ->from('challenge_skills_groups_stacks')
                        ->whereIn('challenge_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('challenge_skills_groups_stacks.type', '0')
                        ->whereNull('challenge_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('challenges.uuid');
            }

            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $challenge_list = $challenge_list->whereIn('duration_id', $request->duration_id);
            }

            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $challenge_list = $challenge_list->whereIn('level_id', $request->level_id);
            }

            if ($request->has('request_status') && !empty($request->request_status)) {
                if (auth('api')->check()) {
                    $status_array = ['accepted', 'pending', 'declined'];
                    if (in_array($request->request_status, $status_array)) {
                        $challenge_list = $challenge_list->join('member_management', 'challenges.id', '=', 'member_management.module_id')
                            ->where(['member_management.module_type' => '2', 'member_management.email' => auth('api')->user()->email]);
                        switch ($request->request_status) {
                            case 'accepted':
                                $challenge_list->where('member_management.invite_status', '1');
                                break;
                            case 'pending':
                                $challenge_list->where('member_management.invite_status', '2');
                                break;
                            case 'declined':
                                $challenge_list->where('member_management.invite_status', '3');
                                break;
                            default:
                                $challenge_list;
                        }
                    }
                }
            }

            if ($request->has('submissions') && !empty($request->submissions) && $request->submissions === 'yes') {
                $challenge_list = $challenge_list->whereHas('submitted_projects', function ($query) {
                    $query->where('is_submitted', '1');
                });
            }

            return $challenge_list;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeBasedOnSlug($slug)
    {
        try {
            return Challenge::where('slug', $slug)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getProjectChallenges($request)
    {
        try {
            $userID = auth()->user()->id;
            $userEmail = auth()->user()->email;
            $challengeUsedIds = Project::where('user_id', $userID)->pluck('challenge_id');
            $challengeMemberIds = MemberManagement::where(['module_type' => '2', 'invite_status' => '1', 'email' => $userEmail])->pluck('module_id');
            $publicChallengeIds = Challenge::where(['language' => $request->language, 'privacy' => '0', 'status' => '1', 'is_open' => '0'])->pluck('id');
            $challengesDiffIds = $challengeMemberIds->merge($publicChallengeIds)->unique()->diff($challengeUsedIds);

            $challenge_list = Challenge::select('uuid', 'title', 'slug', 'media_type', 'media')->whereIn('id', $challengesDiffIds)->where('is_accessible', '1')
                ->whereHas('challenge_timelines', function ($query) {
                    $query->where(function ($q) {
                        $q->where('timeline_type', '1')
                            ->where('start_date', '<', now())
                            ->where(function ($subQuery) {
                                $subQuery->whereNotNull('registration_deadline_date')
                                    ->where('registration_deadline_date', '>', now())
                                    ->orWhereNull('registration_deadline_date');
                            });
                    })->orWhere(function ($q) {
                        $q->where('timeline_type', '0');
                    });
                });
            $challenge_list = self::filterChallengeList($request, $challenge_list);
            $limit = config('site-settings.listing_limit');

            return $challenge_list->limit($limit)->get();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeBasedOnUUID($uuid)
    {
        try {
            return Challenge::where('UUID', $uuid)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectChallengeRequirement($challengeData)
    {
        try {
            $challenge_conditions = [];
            if ($challengeData->challenge_requirements) {
                foreach ($challengeData->challenge_requirements->project_submission_requirement_ids as $project_submission_requirement) {
                    $check_achievement_condition = ProjectSubmissionRequirementService::getProjectSubmissionRequirementByID($challengeData->language, $project_submission_requirement);
                    if ($challengeData->challenge_project_template) {
                        $requirementStatus = '';

                        switch ($check_achievement_condition->id) {
                            case '1':
                                $requirementStatus = false;
                                $challengePitchIds = ChallengePitch::where('template_id', $challengeData->challenge_project_template->template_id)->pluck('id')->all();
                                if (empty($challengePitchIds)) {
                                    $requirementStatus = true;
                                }
                                break;
                            case '2':
                                $requirementStatus = false;
                                $challengeTaskIds = ChallengeTask::where('template_id', $challengeData->challenge_project_template->template_id)->pluck('id')->all();
                                if (empty($challengeTaskIds)) {
                                    $requirementStatus = true;
                                }
                                break;
                            case '3':
                                $requirementStatus = false;
                                break;
                            case '4':
                                $requirementStatus = false;
                                break;
                            case '5':
                                $requirementStatus = false;
                                break;
                        }
                        $projectStatus = ($requirementStatus) ? 'completed' : 'pending';
                        $projectState = [
                            'status'            => $projectStatus,
                            'Requirement Title' => $check_achievement_condition->title,
                        ];

                        $challenge_conditions[$check_achievement_condition->id] = $projectState;
                    }
                }
            }

            return $challenge_conditions;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeBasedOnId($id)
    {
        try {
            return Challenge::where('id', $id)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchChallengeOrganizations($challengeIds)
    {
        try {
            $fetchChallengeOrganizations = Challenge::whereIn('id', $challengeIds)->pluck('organization_id');

            return $fetchChallengeOrganizations;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getTemplate($templateId)
    {
        try {
            $templateData = [];
            if ($templateId == '0') {
                $templateData = [
                    'template_id'    => $templateId,
                    'template_title' => __('responses.any_pitch_template'),
                ];
            } else {
                $template = PitchTemplate::where('id', $templateId)->first();
                if ($template) {
                    $templateData = [
                        'template_id'    => $template->id,
                        'template_title' => $template->title,
                    ];
                }
            }

            return $templateData;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeBasedOnArrayIds($challengeIds)
    {
        try {
            $challenges = Challenge::whereIn('id', $challengeIds)->where('is_accessible', '1')->get();

            return $challenges;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getAll()
    {
        try {
            return Challenge::select();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchRecommendedChallenges($fetchUserSkills, $userData)
    {
        try {
            $getChallengeIdBasedOnSkill = ChallengeSkillsGroupsStackService::getChallengeIdBasedOnSkills($fetchUserSkills);
            $challengeIds = $getChallengeIdBasedOnSkill->unique();
            $fetchRecommendedChallenges = Challenge::whereIn('id', $challengeIds)->where('user_id', '!=', $userData->id)->take(config('site-settings.dashboard_page_limit_max'))->get();

            return $fetchRecommendedChallenges;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchMyChallengeProgress($userData)
    {
        try {
            $inviteStatus = config('constants.member_management_invite_status.accepted');
            $fetchChallenge = MemberManagementService::challengeRequestIds($userData, $inviteStatus);
            $overAllJoinedChallenges = $fetchChallenge->count();
            $completedChallengesCount = ProjectService::fetchCompletedChallenges($fetchChallenge, $userData);
            $inProgressChallengesCount = ProjectService::fetchInProgressChallenges($fetchChallenge, $userData);
            $deadlineMissedChallengesCount = ProjectService::fetchDeadlineMissedChallenges($fetchChallenge, $userData);
            $notStartedChallengesCount = $overAllJoinedChallenges - ($completedChallengesCount + $inProgressChallengesCount + $deadlineMissedChallengesCount);

            $fetchMyChallengeProgress = ['overAllJoined' => $overAllJoinedChallenges, 'completedCount' => $completedChallengesCount, 'inProgressCount' => $inProgressChallengesCount, 'notStartedCount' => $notStartedChallengesCount, 'deadlineMissedCount' => $deadlineMissedChallengesCount];

            return $fetchMyChallengeProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengesBasedOnIds($challengeIds)
    {
        try {
            $getChallengesBasedOnIds = Challenge::whereIn('id', $challengeIds)->get();

            return $getChallengesBasedOnIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchUpComingDeadlineChallenges($challengeIds, $userData)
    {
        try {
            $restrictedDeadlineCollection = collect();
            $flexibleDeadlineCollection = collect();
            $fetchChallenges = self::getChallengesBasedOnIds($challengeIds);
            if ($fetchChallenges->isNotEmpty()) {
                foreach ($fetchChallenges as $fetchChallenge) {
                    if ($fetchChallenge->challenge_timelines) {
                        if ($fetchChallenge->challenge_timelines->timeline_type == '0') {
                            // For flexible challenge
                            if (!empty($fetchChallenge->challenge_timelines->flexible_date_duration)) {
                                $fetchCreatedProject = ProjectService::checkUserChallengeStatus($fetchChallenge->id, $userData->id);
                                if (!empty($fetchCreatedProject)) {
                                    switch ($fetchChallenge->challenge_timelines->flexible_date_duration) {
                                        case 'days':
                                            $convertedDeadline = Carbon::parse($fetchCreatedProject->created_at)->addDays($fetchChallenge->challenge_timelines->flexible_date_number)->toDateTimeString();
                                            break;
                                        case 'weeks':
                                            $convertedDeadline = Carbon::parse($fetchCreatedProject->created_at)->addWeek($fetchChallenge->challenge_timelines->flexible_date_number)->toDateTimeString();
                                            break;
                                        case 'months':
                                            $convertedDeadline = Carbon::parse($fetchCreatedProject->created_at)->addMonth($fetchChallenge->challenge_timelines->flexible_date_number)->toDateTimeString();
                                            break;
                                    }
                                    $flexibleDeadline = [
                                        'id'       => $fetchChallenge->uuid,
                                        'title'    => $fetchChallenge->title,
                                        'slug'     => $fetchChallenge->slug,
                                        'deadline' => UtilityHelper::formatDateTime($convertedDeadline) ?? null,
                                    ];
                                    $flexibleDeadlineCollection->push($flexibleDeadline);
                                }
                            }
                        } elseif ($fetchChallenge->challenge_timelines->timeline_type == '1') {
                            // For restricted challenge
                            $restrictedDeadline = [
                                'id'       => $fetchChallenge->uuid,
                                'title'    => $fetchChallenge->title,
                                'slug'     => $fetchChallenge->slug,
                                'deadline' => UtilityHelper::formatDateTime($fetchChallenge->challenge_timelines->submission_deadline_date) ?? null,
                            ];
                            $restrictedDeadlineCollection->push($restrictedDeadline);
                        }
                    }
                }
            }
            $userDeadlineChallenges = $restrictedDeadlineCollection->merge($flexibleDeadlineCollection);
            if (!empty($userDeadlineChallenges)) {
                $userDeadlineChallenges = $userDeadlineChallenges->sortBy(function ($challenge) {
                    return strtotime($challenge['deadline']);
                });
            }

            return $userDeadlineChallenges->take(5);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
