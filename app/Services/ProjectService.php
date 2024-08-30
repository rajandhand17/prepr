<?php

namespace App\Services;

use App\Events\Project\DeleteProjectAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\Project;
use App\Models\ProjectMemberManagement;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use Carbon\Carbon;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public static function getMyProjectIds($userId)
    {
        try {
            $getMyProjects = Project::where('user_id', $userId)->pluck('id');

            return $getMyProjects;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectList($getProjectIds, $request)
    {
        try {
            $project_list = Project::with('getProjectAssessment')->whereIn('projects.id', $getProjectIds);
            $project_list = self::filterProjectList($project_list, $request);

            return $project_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getDashboardProjectList($getProjectIds)
    {
        try {
            $project_list = Project::with('getProjectAssessment')->whereIn('projects.id', $getProjectIds);

            return $project_list->paginate(config('site-settings.dashboard_pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectListWithoutPagination($getProjectIds, $request)
    {
        try {
            $project_list = Project::with('getProjectAssessment')->whereIn('projects.id', $getProjectIds);
            $project_list = self::filterProjectList($project_list, $request);

            return $project_list->pluck('id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterProjectList($project_list, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $project_list = $project_list->whereSearchFilter($request->search ?? '');
            }
            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $project_list = $project_list->where('projects.privacy', '0');
                        break;
                    case 'private':
                        $project_list = $project_list->where('projects.privacy', '1');
                        break;
                    default:
                        $project_list = $project_list;
                }
            }
            if ($request->has('sort_by_team') && !empty($request->sort_by_team)) {
                switch ($request->sort_by_team) {
                    case 'name':
                        $project_list = $project_list->orderBy('projects.title', 'ASC');
                        break;
                    case 'due_date':
                        $project_list = $project_list->orderBy('projects.title', 'DESC');
                        break;
                    case 'popularity':
                        $project_list = $project_list->withCount('members')->OrderBy('members_count', 'desc');
                        break;
                    default:
                        $project_list = $project_list->whereIn('id', function ($query) {
                            $query->select('project_id')
                                ->from('project_member_management')
                                ->where('invite_status', '1')
                                ->where('inviter_id', auth()->id());
                        });
                        break;
                }
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $project_list = $project_list->orderBy('projects.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $project_list = $project_list->orderBy('projects.title', 'DESC');
                        break;
                    case 'creation_date':
                        $project_list = $project_list->orderBy('projects.created_at', 'ASC');
                        break;
                    case 'relevance' :
                        $project_list = self::getRelevence(auth()->user(), $project_list);
                        break;
                    default:
                        $project_list = $project_list->orderBy('projects.id', 'ASC');
                }
            }

            if ($request->has('skills') && !empty($request->skills)) {
                $projectIds = ProjectSkillsService::getProjectIdsBasedOnSkills($request->skills);
                $project_list = $project_list->whereIn('projects.id', $projectIds);
            }

            if ($request->has('level') && !empty($request->level)) {
                $getChallenges = ChallengeService::getChallengesBasedOnLevelId($request->level);
                $project_list = $project_list->whereIn('projects.challenge_id', $getChallenges);
            }

            if ($request->has('duration') && !empty($request->duration)) {
                $getChallengesBasedOnDuration = ChallengeService::getChallengesBasedOnDuration($request->duration);
                $project_list = $project_list->whereIn('projects.challenge_id', $getChallengesBasedOnDuration);
            }

            if ($request->has('request_status') && !empty($request->request_status)) {
                $requestStatus = ProjectMemberManagementService::getAllRequestsData($request->request_status);
                $project_list = $project_list->whereIn('projects.id', $requestStatus);
            }

            if ($request->has('status') && !empty($request->status)) {
                $status_array = ['in_progress', 'submitted', 'challenge_closed', 'assessment_details_available'];
                if (in_array($request->status, $status_array)) {
                    $projectStatusIds = $project_list->get()->map(function ($projectData) use ($request) {
                        $projectIds = [];
                        switch ($request->status) {
                            case 'in_progress':
                                $projectRequirementData = self::checkProjectRequirementCompleted($projectData);
                                if ($projectRequirementData === false) {
                                    $projectIds = $projectData->id;
                                }
                                break;

                            case 'submitted':
                                if ($projectData->is_submitted === '1') {
                                    $projectIds = $projectData->id;
                                }
                                break;

                            case 'challenge_closed':
                                $getChallenge = Challenge::find($projectData->challenge_id)->is_open;
                                if ($getChallenge !== '0') {
                                    $projectIds = $projectData->id;
                                }
                                break;

                            case 'assessment_details_available':
                                $projectAssessmentData = ChallengeAssessmentUserService::getProjectAssessmentData($projectData, auth()->user()->id);
                                if ($projectAssessmentData['assessment_status'] === 'published') {
                                    $projectIds = $projectData->id;
                                }
                                break;
                        }

                        return $projectIds;
                    });
                    $project_list = $project_list->whereIn('projects.id', $projectStatusIds->filter());
                }
            }

            return $project_list;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRelevence($user, $project_list)
    {
        try {
        // Retrieve user skills
            $userSkills = $user->userSkills->pluck('skill')->toArray(); // Assuming 'skill' is the skill ID
            $project_list = $project_list->addSelect([
                'matching_skills_count' => function ($query) use ($userSkills) {
                    $query->selectRaw('COUNT(*)')
                        ->from('project_skills')
                        ->whereColumn('project_skills.project_id', 'projects.id')
                        ->whereIn('project_skills.skill_id', $userSkills);
                },
            ]);

            // Order by the matching skills count in descending order
            $project_list = $project_list->orderByDesc('matching_skills_count');

            return $project_list;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadCoverImage($coverImage)
    {
        try {
            $upload_project_cover_image = FileUploadHelper::uploadImageToS3($coverImage, 'project');
            if ($upload_project_cover_image == false) {
                return false;
            }

            return $upload_project_cover_image;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkProjectCreatedWithChallenge($challengeId)
    {
        try {
            $checkProjectCreatedWithChallenge = Project::where(['user_id' => auth()->user()->id, 'challenge_id' => $challengeId])->exists();

            return $checkProjectCreatedWithChallenge;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createProject($request, $uploadedCoverImage)
    {
        try {
            switch ($request->is_view_enabled) {
                case 'yes':
                    $viewEnabled = config('constants.project_view_enabled.yes');
                    break;
                case 'no':
                    $viewEnabled = config('constants.project_view_enabled.no');
                    break;
                default:
                    $viewEnabled = config('constants.project_view_enabled.no');
                    break;
            }

            switch ($request->is_download_enabled) {
                case 'yes':
                    $downloadEnabled = config('constants.project_download_enabled.yes');
                    break;
                case 'no':
                    $downloadEnabled = config('constants.project_download_enabled.no');
                    break;
                default:
                    $downloadEnabled = config('constants.project_download_enabled.no');
                    break;
            }

            switch ($request->media_type) {
                case 'image':
                    $mediaType = config('constants.project_media_type.image');
                    break;
                case 'embedded':
                    $mediaType = config('constants.project_media_type.embedded');
                    break;
                case 'video':
                    $mediaType = config('constants.project_media_type.video');
                    break;
                default:
                    $mediaType = config('constants.project_media_type.image');
                    break;
            }

            switch ($request->privacy) {
                case 'public':
                    $projectPrivacy = config('constants.project_privacy.public');
                    break;
                case 'private':
                    $projectPrivacy = config('constants.project_privacy.private');
                    break;
                default:
                    $projectPrivacy = config('constants.project_privacy.public');
                    break;
            }

            $labId = null;
            if ($request->has('lab_id')) {
                $labId = LabService::getLabBasedOnUUID($request->lab_id)->id;
            }
            $challengeId = ChallengeService::getChallengeBasedOnUUID($request->challenge_id)->id;

            $model = new Project();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $createProject = new Project();
            $createProject->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $createProject->language = $request->language;
            $createProject->user_id = auth()->user()->id;
            $createProject->title = $request->title;
            $createProject->slug = $slug;
            $createProject->description = $request->description;
            $createProject->is_view_enabled = $viewEnabled;
            $createProject->is_download_enabled = $downloadEnabled;
            $createProject->media_type = $mediaType;
            $createProject->media = $uploadedCoverImage;
            $createProject->privacy = $projectPrivacy;
            $createProject->challenge_id = $challengeId;
            $createProject->lab_id = $labId;
            $createProject->save();

            return $createProject;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectBasedOnSlug($slug)
    {
        try {
            return Project::where('slug', $slug)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $checkProjectName = Project::where('title', $title)->first();
            if ($checkProjectName) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateProject($slug, $request, $uploadedCoverImage)
    {
        try {
            $updateProject = Project::where('slug', $slug)->first();
            if ($updateProject !== null) {
                switch ($request->is_view_enabled) {
                    case 'yes':
                        $viewEnabled = config('constants.project_view_enabled.yes');
                        break;
                    case 'no':
                        $viewEnabled = config('constants.project_view_enabled.no');
                        break;
                    default:
                        $viewEnabled = $updateProject->view_enabled;
                        break;
                }

                switch ($request->is_download_enabled) {
                    case 'yes':
                        $downloadEnabled = config('constants.project_download_enabled.yes');
                        break;
                    case 'no':
                        $downloadEnabled = config('constants.project_download_enabled.no');
                        break;
                    default:
                        $downloadEnabled = $updateProject->download_enabled;
                        break;
                }

                switch ($request->media_type) {
                    case 'image':
                        $mediaType = config('constants.project_media_type.image');
                        break;
                    case 'embedded':
                        $mediaType = config('constants.project_media_type.embedded');
                        break;
                    case 'video':
                        $mediaType = config('constants.project_media_type.video');
                        break;
                    default:
                        $mediaType = $updateProject->media_type;
                        break;
                }

                switch ($request->privacy) {
                    case 'public':
                        $projectPrivacy = config('constants.project_privacy.public');
                        break;
                    case 'private':
                        $projectPrivacy = config('constants.project_privacy.private');
                        break;
                    default:
                        $projectPrivacy = $updateProject->privacy;
                        break;
                }

                $labId = $updateProject->lab_id;
                if ($request->has('lab_id')) {
                    $checkLab = LabService::getLabBasedOnUUID($request->lab_id);
                    if ($checkLab != null) {
                        $labId = $checkLab->id;
                    }
                }

                $updateProject->language = ($request->has('language')) ? $request->language : $updateProject->language;
                $updateProject->title = ($request->has('title')) ? $request->title : $updateProject->title;
                $updateProject->description = ($request->has('description')) ? $request->description : $updateProject->description;
                $updateProject->is_view_enabled = $viewEnabled;
                $updateProject->is_download_enabled = $downloadEnabled;
                $updateProject->media_type = $mediaType;
                $updateProject->media = $uploadedCoverImage;
                $updateProject->privacy = $projectPrivacy;
                $updateProject->challenge_id = $updateProject->challenge_id;
                $updateProject->lab_id = $labId;
                $updateProject->save();

                return $updateProject;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function projectRequirements($projectData)
    {
        try {
            $challengeData = ChallengeService::getChallengeBasedOnId($projectData->challenge_id);

            $challenge_conditions = [];
            if ($challengeData->challenge_requirements) {
                foreach ($challengeData->challenge_requirements->project_submission_requirement_ids as $project_submission_requirement) {
                    $check_achievement_condition = ProjectSubmissionRequirementService::getProjectSubmissionRequirementByID($challengeData->language, $project_submission_requirement);
                    if ($challengeData->challenge_project_template) {
                        $requirementStatus = '';

                        switch ($check_achievement_condition->id) {
                            case '1':
                                $type = 'pitch';
                                $requirementStatus = ProjectPitchService::checkProjectPitch($projectData->id, $challengeData->challenge_project_template->template_id);
                                break;
                            case '2':
                                $type = 'task';
                                $requirementStatus = ProjectPitchService::checkProjectTask($projectData->id, $challengeData->challenge_project_template->template_id);
                                break;
                            case '3':
                                $type = 'links';
                                $requirementStatus = ProjectExternalLinksService::checkProjectExternalLink($projectData->id);
                                break;
                            case '4':
                                $type = 'gallery';
                                $requirementStatus = ProjectFileService::checkProjectGallery($projectData->id);
                                break;
                            case '5':
                                $type = 'file';
                                $requirementStatus = ProjectFileService::checkProjectFile($projectData->id);
                                break;
                        }
                        $projectStatus = ($requirementStatus) ? 'completed' : 'pending';
                        $projectState = [
                            'type'              => $type,
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

    public function deleteProject($projectId)
    {
        try {
            $project = Project::find($projectId)->delete();
            if ($project) {
                $projectAssociatedData = event(new DeleteProjectAssociatedData($projectId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkProjectRequirementCompleted($projectData)
    {
        try {
            $submitEnabled = true;
            $projectRequirements = self::projectRequirements($projectData);
            if ($projectRequirements !== []) {
                foreach ($projectRequirements as $projectRequirement) {
                    if ($projectRequirement['status'] === 'pending') {
                        $submitEnabled = false;
                    }
                }
            }

            return $submitEnabled;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkSubmisstionDate($projectData)
    {
        try {
            $dates = $projectData->challenge->challenge_timelines;
            if ($dates->timeline_type == '1') {
                if ($dates->submission_deadline_date < date('Y-m-d H:i:s')) {
                    $lateSubmission = 'yes';
                } else {
                    $lateSubmission = 'no';
                }
            } else {
                $dateResult = ' ';
                //for flexible challenge check if date if passed from duration
                if ($dates->flexible_date_duration == 'days') {
                    $dateCount = $dates->flexible_date_number;
                } elseif ($dates->flexible_date_duration == 'weeks') {
                    $dateCount = $dates->flexible_date_number * 7;
                } elseif ($dates->flexible_date_duration == 'months') {
                    $dateCount = $dates->flexible_date_number * 30;
                }
                $durationDate = date_create(date('Y-m-d', strtotime($projectData->created_at.' + '.$dateCount.'days')));
                $date = date_create(date('Y-m-d'));
                $dateResult = ($durationDate < $date);
                if ($dateResult) {
                    $lateSubmission = 'yes';
                } else {
                    $lateSubmission = 'no';
                }
            }

            return $lateSubmission;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function submitProject($projectData, $checkLateSubmission, $request)
    {
        try {
            if ($checkLateSubmission == 'yes') {
                if ($request->late_submission_msg == null || !$request->has('late_submission_msg')) {
                    $submitted = 'no';

                    return $submitted;
                }
                $projectData->is_submitted = '2';
                $projectData->late_submission_reason = $request->late_submission_msg;
                $projectData->save();
            } else {
                $projectData->is_submitted = '1';
                $projectData->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getAssessedProjectIds($getAllChallengeIds, $userData)
    {
        try {
            $getProjectIdBasedOnMember = ProjectMemberManagement::where('email', $userData->email)->pluck('project_id');
            $getOwnProjectIds = self::getMyProjectIds($userData->id);

            $collaborateProjectIds = $getOwnProjectIds->merge($getProjectIdBasedOnMember)->unique();
            $fetchSubmittedProjectIds = Project::whereNotIn('id', $collaborateProjectIds)->whereIn('challenge_id', $getAllChallengeIds)->where('is_submitted', '1')->pluck('id');
            $assessedProjectIds = [];
            if (!empty($fetchSubmittedProjectIds)) {
                foreach ($fetchSubmittedProjectIds as $fetchSubmittedProjectId) {
                    $projectData = Project::find($fetchSubmittedProjectId);
                    $assessedCheck = ChallengeAssessmentUserService::getProjectAssessmentData($projectData, $userData->id);
                    if ($assessedCheck['assessment_status'] === 'published') {
                        $assessedProjectIds[] = $projectData->id;
                    }
                }
            }

            return collect($assessedProjectIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPendingProjectIds($getAllChallengeIds, $userData)
    {
        try {
            $getProjectIdBasedOnMember = ProjectMemberManagement::where('email', $userData->email)->pluck('project_id');
            $getOwnProjectIds = self::getMyProjectIds($userData->id);

            $collaborateProjectIds = $getOwnProjectIds->merge($getProjectIdBasedOnMember)->unique();
            $fetchSubmittedProjectIds = Project::whereNotIn('id', $collaborateProjectIds)->whereIn('challenge_id', $getAllChallengeIds)->where('is_submitted', '1')->pluck('id');
            $pendingProjectIds = [];
            if (!empty($fetchSubmittedProjectIds)) {
                foreach ($fetchSubmittedProjectIds as $fetchSubmittedProjectId) {
                    $projectData = Project::find($fetchSubmittedProjectId);
                    $assessedCheck = ChallengeAssessmentUserService::getProjectAssessmentData($projectData, $userData->id);
                    if ($assessedCheck['assessment_status'] !== 'published') {
                        $pendingProjectIds[] = $projectData->id;
                    }
                }
            }

            return collect($pendingProjectIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateProjectRecruitingStatus($projectId, $request)
    {
        try {
            $projectUpdate = Project::find($projectId);
            switch ($request->recruiting_status) {
                case 'yes':
                    $recruiting_status = '0';
                    break;
                case 'no':
                    $recruiting_status = '1';
                    break;
                default:
                    $recruiting_status = '0';
                    break;
            }

            if ($projectUpdate->recruiting_status !== $recruiting_status) {
                $activity = auth()->user()->full_name.' '.__('responses.project_updated_recruiting');
                ProjectHistoryService::storeHistory($projectUpdate->id, auth()->user()->id, $activity);
            }
            $projectUpdate->recruiting_status = $recruiting_status;
            $projectUpdate->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkProjectRole($projectData)
    {
        try {
            $userEmail = auth()->user()->email;
            $userId = auth()->user()->id;

            $checkProjectMember = ProjectMemberManagement::where(['project_id' => $projectData->id, 'email' => $userEmail])->first();
            $checkProjectOwner = Project::where(['id' => $projectData->id, 'user_id' => $userId])->first();

            $assessedChallengeIds = ChallengeAssessmentService::getAllChallengeIds(auth()->user());
            $fetchSubmittedProjectIds = Project::whereIn('challenge_id', $assessedChallengeIds)->where(['id' => $projectData->id, 'is_submitted' => '1'])->first();

            if ($checkProjectOwner || $checkProjectMember) {
                $project_role = 'submitter';
            } elseif ($fetchSubmittedProjectIds) {
                $project_role = 'assessor';
            } else {
                $project_role = 'none';
            }

            return $project_role;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getBrowsersListing($userData)
    {
        try {
            $myProjects = ProjectService::getMyProjectIds($userData->id);
            $teamProjects = ProjectMemberManagementService::getAcceptedInvitesProjectIds($userData);
            $invitesProjects = ProjectMemberManagementService::getPendingInvitesProjectIds($userData);
            $projectCollection = new Collection();
            if ($myProjects) {
                $projectCollection = $projectCollection->concat($myProjects);
            }
            if ($teamProjects) {
                $projectCollection = $projectCollection->concat($teamProjects);
            }
            if ($invitesProjects) {
                $projectCollection = $projectCollection->concat($invitesProjects);
            }

            $projectIds = Project::whereNotIn('id', $projectCollection)->where('projects.privacy', '0')->pluck('id');

            return $projectIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterTeamMatesProjectList($project_list, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $project_list = $project_list->where('projects.title', 'like', '%'.$request->search.'%');
            }
            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $project_list = $project_list->where('projects.privacy', '0');
                        break;
                    case 'private':
                        $project_list = $project_list->where('projects.privacy', '1');
                        break;
                    default:
                        $project_list = $project_list;
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $project_list = $project_list->whereIn('projects.id', function ($query) use ($request) {
                    $query->select('project_id')
                        ->from('project_skills')
                        ->whereIn('project_skills.skill_id', $request->skills)
                        ->whereNull('project_skills.deleted_at')
                        ->distinct(); // Move distinct() here
                });
            }
            if ($request->has('challenge_duration') && !empty($request->challenge_duration) && is_array($request->challenge_duration)) {
                $project_list = $project_list->whereIn('projects.challenge_id', function ($query) use ($request) {
                    $query->select('challenges.id')
                        ->from('challenges')
                        ->whereIn('challenges.duration_id', $request->challenge_duration)
                        ->whereNull('challenges.deleted_at');
                });
            }
            if ($request->has('challenge_level') && !empty($request->challenge_level) && is_array($request->challenge_level)) {
                $project_list = $project_list->whereIn('projects.challenge_id', function ($query) use ($request) {
                    $query->select('challenges.id')
                        ->from('challenges')
                        ->whereIn('challenges.level_id', $request->challenge_level)
                        ->whereNull('challenges.deleted_at');
                });
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $project_list = $project_list->orderBy('projects.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $project_list = $project_list->orderBy('projects.title', 'DESC');
                        break;
                    case 'creation_date':
                        $project_list = $project_list->orderBy('projects.created_at', 'ASC');
                        break;
                    default:
                        $project_list = $project_list->orderBy('projects.id', 'ASC');
                }
            }

            return $project_list;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getMatchedTeams($request)
    {
        try {
            $getProjectIds = ProjectMemberManagementService::getMatchedTeams();
            $getMyProjects = Project::whereIn('id', $getProjectIds);
            $project_list = self::filterTeamMatesProjectList($getMyProjects, $request);

            return $project_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectIds($projectIds)
    {
        try {
            return Project::whereNotIn('id', $projectIds)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectBasedOnUuid($projectUuid)
    {
        try {
            return Project::where('uuid', $projectUuid)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchSubmittedProjectsChallengeId($userData)
    {
        try {
            $getProjectIdBasedOnMember = ProjectMemberManagement::where('email', $userData->email)->pluck('project_id');
            $getOwnProjectIds = self::getMyProjectIds($userData->id);

            $collaborateProjectIds = $getOwnProjectIds->merge($getProjectIdBasedOnMember)->unique();
            $fetchSubmittedProjectIds = Project::whereIn('id', $collaborateProjectIds)->where('is_submitted', '1')->pluck('challenge_id');

            return $fetchSubmittedProjectIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectBasedOnId($id)
    {
        try {
            $getMyProjects = Project::where('id', $id)->first();

            return $getMyProjects;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkUserChallengeStatus($challengeId, $userId)
    {
        try {
            $getUserProject = Project::where(['user_id' => $userId, 'challenge_id' => $challengeId])->first();

            return $getUserProject;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchCompletedChallenges($challengeIds, $userData)
    {
        try {
            $fetchCompletedChallenges = Project::whereIn('challenge_id', $challengeIds)->where(['user_id' => $userData->id, 'is_submitted' => '1'])->count();

            return $fetchCompletedChallenges;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchInProgressChallenges($challengeIds, $userData)
    {
        $fetchInProgressChallenges = self::fetchChallenges($challengeIds, $userData, 'in_progress');

        return $fetchInProgressChallenges;
    }

    public static function fetchDeadlineMissedChallenges($challengeIds, $userData)
    {
        $fetchDeadlineMissedChallenges = self::fetchChallenges($challengeIds, $userData, 'deadline_missed');

        return $fetchDeadlineMissedChallenges;
    }

    public static function fetchChallenges($challengeIds, $userData, $type)
    {
        try {
            $countChallenges = 0;
            $projects = Project::whereIn('challenge_id', $challengeIds)->where(['user_id' => $userData->id, 'is_submitted' => '0'])->get();

            if ($projects->isNotEmpty()) {
                foreach ($projects as $project) {
                    $templateData = $project->getProjectTemplate->template_id == '0' ? $project->getProjectIdBasedTemplate ?? $project->getProjectTemplate : $project->getProjectTemplate;
                    $challengeDetail = ChallengeService::getChallengeDetailedBasedOnChallenges($project->challenge_id, $project->created_at, $templateData);
                    $challengeDueDate = Carbon::parse($challengeDetail['due_date']);
                    if (($type == 'in_progress' && $challengeDueDate->greaterThanOrEqualTo(now())) || ($type == 'deadline_missed' && $challengeDueDate->lessThanOrEqualTo(now()))) {
                        $countChallenges++;
                    }
                }
            }

            return $countChallenges;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectBasedOnChallengeIds($challengeIds)
    {
        try {
            $fetchProjectBasedOnChallengeIds = Project::whereIn('challenge_id', $challengeIds)->get();

            return $fetchProjectBasedOnChallengeIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchSubmittedProjectids($challengeIds)
    {
        try {
            $fetchSubmittedProjectids = Project::whereIn('challenge_id', $challengeIds)->where('is_submitted', '1')->pluck('id');

            return $fetchSubmittedProjectids;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectIdsBasedOnChallenge($challengeId)
    {
        try {
            $fetchProjectIdsBasedOnChallenge = Project::where('challenge_id', $challengeId)->pluck('id');

            return $fetchProjectIdsBasedOnChallenge;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectsByUserId($userId)
    {
        try {
            $getUserProjects = Project::where('user_id', $userId)
            ->paginate(config('site-settings.association_pagination_per_page'));

            return $getUserProjects;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectLabAssociation($labId)
    {
        try {
            $project_list = Project::with('getProjectAssessment')->where('projects.lab_id', $labId);

            return $project_list->paginate(config('site-settings.association_pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
