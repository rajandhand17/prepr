<?php

namespace App\Repositories\Api\Project;

use App\Jobs\UserAchievement\ProcessChallengePathAchievementJob;
use App\Notifications\ProjectCreatedNotification;
use App\Services\AchievementService;
use App\Services\ChallengeAssessmentUserService;
use App\Services\Manage\ChallengeAchievementService;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\EmailTemplateService;
use App\Services\ProjectAdditionalInfoService;
use App\Services\ProjectExternalLinksService;
use App\Services\ProjectFileService;
use App\Services\ProjectHistoryService;
use App\Services\ProjectMemberManagementService;
use App\Services\ProjectPitchService;
use App\Services\ProjectService;
use App\Services\ProjectSkillsService;
use App\Services\ProjectSocialActivitiesService;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectRepository implements ProjectInterface
{
    private $projectService;
    private $challengeService;
    private $projectPitchService;
    private $projectFileService;
    private $projectExternalLinksService;
    private $projectAdditionalInfoService;
    private $projectMemberManagementService;
    private $projectSocialActivitiesService;
    private $challengeAchievementService;
    private $achievementService;
    private $challengeAssessmentService;
    private $challengeAssessmentUserService;
    private $projectSkillsService;
    private $projectHistoryService;
    private $userService;

    public function __construct(ProjectService $projectService, ChallengeService $challengeService, ProjectPitchService $projectPitchService, ProjectFileService $projectFileService, ProjectExternalLinksService $projectExternalLinksService, ProjectAdditionalInfoService $projectAdditionalInfoService, ProjectMemberManagementService $projectMemberManagementService, ProjectSocialActivitiesService $projectSocialActivitiesService, ChallengeAchievementService $challengeAchievementService, AchievementService $achievementService, ChallengeAssessmentService $challengeAssessmentService, ChallengeAssessmentUserService $challengeAssessmentUserService, ProjectSkillsService $projectSkillsService, ProjectHistoryService $projectHistoryService, UserService $userService)
    {
        $this->projectService = $projectService;
        $this->challengeService = $challengeService;
        $this->projectPitchService = $projectPitchService;
        $this->projectFileService = $projectFileService;
        $this->projectExternalLinksService = $projectExternalLinksService;
        $this->projectAdditionalInfoService = $projectAdditionalInfoService;
        $this->projectMemberManagementService = $projectMemberManagementService;
        $this->projectSocialActivitiesService = $projectSocialActivitiesService;
        $this->challengeAchievementService = $challengeAchievementService;
        $this->achievementService = $achievementService;
        $this->challengeAssessmentService = $challengeAssessmentService;
        $this->challengeAssessmentUserService = $challengeAssessmentUserService;
        $this->projectSkillsService = $projectSkillsService;
        $this->projectHistoryService = $projectHistoryService;
        $this->userService = $userService;
    }

    public function getMyProjectIds($userId)
    {
        try {
            return $this->projectService->getMyProjectIds($userId);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getFavouriteProjectIds($userId)
    {
        try {
            return $this->projectSocialActivitiesService->getFavouriteProjectIds($userId);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAcceptedInvitesProjectIds($userData)
    {
        try {
            return $this->projectMemberManagementService->getAcceptedInvitesProjectIds($userData);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPendingInvitesProjectIds($userData)
    {
        try {
            return $this->projectMemberManagementService->getPendingInvitesProjectIds($userData);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAssessedProjectIds($userData)
    {
        try {
            $projectIds = [];
            $getAllChallengeIds = $this->challengeAssessmentService->getAllChallengeIds($userData);
            if (!empty($getAllChallengeIds)) {
                $projectIds = $this->projectService->getAssessedProjectIds($getAllChallengeIds, $userData);
            }

            return $projectIds;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPendingProjectIds($userData)
    {
        try {
            $projectIds = [];
            $getAllChallengeIds = $this->challengeAssessmentService->getAllChallengeIds($userData);
            if (!empty($getAllChallengeIds)) {
                $projectIds = $this->projectService->getPendingProjectIds($getAllChallengeIds, $userData);
            }

            return $projectIds;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProjectList($getProjectIds, $request)
    {
        try {
            return $this->projectService->getProjectList($getProjectIds, $request);
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadCoverImage($coverImage)
    {
        try {
            return $this->projectService->uploadCoverImage($coverImage);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createProject($request, $uploadedCoverMedia)
    {
        try {
            //field entry for owner's data entry in project member management
            $userId = auth()->user()->id;
            $userEmail = auth()->user()->email;
            $userFullName = auth()->user()->full_name;
            $inviteType = '1';
            $inviteStatus = '1';
            $emailStatus = '1';
            $accessLevel = '2';
            $getTemplate = EmailTemplateService::getEmailTemplate(config('constants.email_template_type.invitation'), config('constants.member_management_component_type.project'), $request->language);

            $createProject = DB::transaction(function () use ($request, $uploadedCoverMedia, $userId, $userEmail, $userFullName, $inviteType, $inviteStatus, $emailStatus, $accessLevel, $getTemplate) {
                $createProject = $this->projectService->createProject($request, $uploadedCoverMedia);
                $getTemplate->body_content = str_replace('user_name', $userFullName, str_replace('component_title', $createProject->title, $getTemplate->body_content));
                $subject = $getTemplate->subject;
                $emailBody = $getTemplate->body_content;
                $createProjectMember = $this->projectMemberManagementService->feedParticipatesData($createProject->id, $userId, $userEmail, $userFullName, $inviteType, $inviteStatus, $emailStatus, $accessLevel, $subject, $emailBody);

                return [
                    'createProject'         => $createProject,
                    'createProjectMember'   => $createProjectMember,
                ];
            });
            if ($createProject['createProject'] && $createProject['createProjectMember']) {
                $activity = auth()->user()->full_name.' '.__('responses.project_created_activty').' '.$createProject['createProject']->title;
                self::storeHistory($createProject['createProject']->id, $userId, $activity);
                $user = UserService::getUserById(auth()->user()->id);
                $user->notify(new ProjectCreatedNotification(__('responses.noti_project_created'), __('responses.noti_project_created_message')));
                DB::commit();

                return $createProject['createProject'];
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function getProjectBasedOnSlug($slug)
    {
        try {
            return $this->projectService->getProjectBasedOnSlug($slug);
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            return $this->projectService->checkNameExistsOrNot($title);
        } catch (Exception $e) {
            return false;
        }
    }

    public function projectPitchTask($projectId, $request)
    {
        try {
            $addProjectPitchTaskAnswer = DB::transaction(function () use ($projectId, $request) {
                $addProjectPitchTaskAnswer = $this->projectPitchService->addProjectPitchTaskAnswer($projectId, $request);

                return $addProjectPitchTaskAnswer;
            });

            if ($addProjectPitchTaskAnswer) {
                DB::commit();

                return true;
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function projectProjectFile($projectId, $request)
    {
        try {
            $addProjectFile = DB::transaction(function () use ($projectId, $request) {
                $addProjectFile = $this->projectFileService->addProjectFile($projectId, $request);

                return $addProjectFile;
            });

            if ($addProjectFile) {
                DB::commit();

                return true;
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function updateProject($slug, $request, $uploadedCoverMedia)
    {
        try {
            $updateProject = DB::transaction(function () use ($slug, $request, $uploadedCoverMedia) {
                $updateProject = $this->projectService->updateProject($slug, $request, $uploadedCoverMedia);

                return [
                    'updateProject' => $updateProject,
                ];
            });
            if ($updateProject['updateProject']) {
                $activity = auth()->user()->full_name.' '.__('responses.project_updated_activty').' '.$updateProject['updateProject']->title;
                self::storeHistory($updateProject['updateProject']->id, auth()->user()->id, $activity);
                DB::commit();

                return $updateProject['updateProject'];
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function addUpdateExternalLink($request, $projectId)
    {
        try {
            $externalLink = DB::transaction(function () use ($request, $projectId) {
                $externalLink = $this->projectExternalLinksService->addUpdateExternalLink($request, $projectId);

                return [
                    'externalLink' => $externalLink,
                ];
            });
            if ($externalLink['externalLink']) {
                DB::commit();

                return $externalLink['externalLink'];
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function addUpdateAdditionalInfo($request, $projectId)
    {
        try {
            $additionalInfo = DB::transaction(function () use ($request, $projectId) {
                $additionalInfo = $this->projectAdditionalInfoService->addUpdateAdditionalInfoService($request, $projectId);

                return [
                    'additionalInfo' => $additionalInfo,
                ];
            });
            if ($additionalInfo['additionalInfo']) {
                DB::commit();

                return $additionalInfo['additionalInfo'];
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function projectRequirements($projectData)
    {
        try {
            return $this->projectService->projectRequirements($projectData);

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteProject($projectId)
    {
        try {
            DB::beginTransaction();

            $deleteProject = $this->projectService->deleteProject($projectId);
            if ($deleteProject == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function checkProjectRequirementCompleted($projectData)
    {
        try {
            return $this->projectService->checkProjectRequirementCompleted($projectData);
        } catch (Exception $e) {
            return false;
        }
    }

    public function submitProject($projectData)
    {
        try {
            $fetchAcceptedMemberIds = $this->projectMemberManagementService->fetchAcceptedMemberIds($projectData->id);
            $fetchChallenge = $this->challengeService->getChallengeBasedOnId($projectData->challenge_id);
            $fetchChallengeAchievement = $this->challengeAchievementService->fetchChallengeAchievement($projectData->challenge_id);

            $submitProject = DB::transaction(function () use ($fetchAcceptedMemberIds, $fetchChallengeAchievement, $fetchChallenge, $projectData) {
                $submitProject = $this->projectService->submitProject($projectData);
                $addAchievement = $this->achievementService->addAchievement($fetchAcceptedMemberIds, $fetchChallengeAchievement, $fetchChallenge, $projectData);
                $updateUserPoint = $this->userService->updateUserPoint($fetchAcceptedMemberIds, $fetchChallengeAchievement->achievement_points);

                return [
                    'submitProject'  => $submitProject,
                    'addAchievement' => $addAchievement,
                    'updateUserPoint'=> $updateUserPoint,
                ];
            });

            if (
                $submitProject['submitProject'] &&
                $submitProject['addAchievement'] &&
                $submitProject['updateUserPoint']
            ) {
                $activity = auth()->user()->full_name.' '.__('responses.project_submit_activty').' '.$projectData->title;
                self::storeHistory($projectData->id, auth()->user()->id, $activity);
                dispatch(new ProcessChallengePathAchievementJob($fetchAcceptedMemberIds, $fetchChallenge->id));
                DB::commit();

                return $submitProject['submitProject'];
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->projectSocialActivitiesService->getColumnNameValue($action);
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkSocialActivity($projectId, $column, $action)
    {
        try {
            return $this->projectSocialActivitiesService->checkSocialActivity($projectId, $column, $action);
        } catch (Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($projectId, $column, $action)
    {
        try {
            return $this->projectSocialActivitiesService->captureSocialActivity($projectId, $column, $action);
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkAssessmentChallenges($userData)
    {
        try {
            return $this->challengeAssessmentService->getAllChallengeIds($userData);
        } catch (Exception $e) {
            return false;
        }
    }

    public function captureProjectAssessment($projectData, $userData, $request)
    {
        try {
            $fetchChallengeData = $this->challengeService->getChallengeBasedOnId($projectData->challenge_id);
            if ($fetchChallengeData->challenge_assessment_criteria->isNotEmpty()) {
                $challengeAssessment = $fetchChallengeData->challenge_assessment_criteria;
                $addProjectEvaluation = DB::transaction(function () use ($challengeAssessment, $projectData, $userData, $request) {
                    $addProjectEvaluation = $this->challengeAssessmentUserService->addProjectEvaluation($challengeAssessment, $projectData, $userData, $request);

                    return $addProjectEvaluation;
                });

                if ($addProjectEvaluation) {
                    DB::commit();

                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function captureProjectAIAssessment($projectData, $userData, $request)
    {
        try {
            $fetchChallengeData = $this->challengeService->getChallengeBasedOnId($projectData->challenge_id);
            if ($fetchChallengeData->challenge_assessment_criteria->isNotEmpty()) {
                $challengeAssessment = $fetchChallengeData->challenge_assessment_criteria;
                $addProjectAIEvaluation = $this->aiService->addAIProjectEvaluation($challengeAssessment, $projectData, $userData, $request);

                if ($addProjectAIEvaluation) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            Log::error('Error in captureProjectAIAssessment in ProjectRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function assessProjectAI($request)
    {
        try {
            $addProjectAIEvaluation = DB::transaction(function () use ($request) {
                $addProjectAIEvaluation = $this->challengeAssessmentUserService->addProjectEvaluation($challengeAssessment = null, $projectData = null, $userData = null, $request);

                return $addProjectAIEvaluation;
            });

            if ($addProjectAIEvaluation) {
                DB::commit();

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Error in assessProjectAI in ProjectRepository.php: '.$e->getMessage());
            DB::rollBack();

            return false;
        }
    }

    public function addUpdateProjectSkillsRecruitingStatus($projectId, $request)
    {
        try {
            $addUpdateProjectSkillsRecruitingStatus = DB::transaction(function () use ($projectId, $request) {
                $addUpdateProjectSkills = $this->projectSkillsService->addUpdateProjectSkills($projectId, $request);
                $updateProjectRecruitingStatus = $this->projectService->updateProjectRecruitingStatus($projectId, $request);

                return [
                    'addUpdateProjectSkills'        => $addUpdateProjectSkills,
                    'updateProjectRecruitingStatus' => $updateProjectRecruitingStatus,
                ];
            });

            if (
                $addUpdateProjectSkillsRecruitingStatus['addUpdateProjectSkills'] &&
                $addUpdateProjectSkillsRecruitingStatus['updateProjectRecruitingStatus']
            ) {
                DB::commit();

                return true;
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function checkChallengeProjectAssessment($projectDataId, $userData)
    {
        try {
            return $this->challengeAssessmentUserService->checkChallengeProjectAssessment($projectDataId, $userData);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteChallengeProjectAssessment($projectDataId, $userData)
    {
        try {
            $deleteProjectAssessment = DB::transaction(function () use ($projectDataId, $userData) {
                $deleteAssessment = $this->challengeAssessmentUserService->deleteChallengeProjectAssessment($projectDataId, $userData);

                return $deleteAssessment;
            });

            if ($deleteProjectAssessment) {
                DB::commit();

                return true;
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function deleteProjectMedia($request, $projectDataId)
    {
        try {
            $deleteProjectMedia = DB::transaction(function () use ($request, $projectDataId) {
                if ($request->type === 'url') {
                    $deleteMedia = $this->projectExternalLinksService->deleteProjectMediaLink($request, $projectDataId);
                } else {
                    $deleteMedia = $this->projectFileService->deleteProjectMediaFile($request, $projectDataId);
                }

                return $deleteMedia;
            });

            if ($deleteProjectMedia) {
                DB::commit();

                return true;
            }

            return false;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function storeHistory($projectId, $userId, $activity)
    {
        try {
            return $this->projectHistoryService->storeHistory($projectId, $userId, $activity);
        } catch (Exception $e) {
            return false;
        }
    }

    public function fetchProjectHistory($projectId)
    {
        try {
            return $this->projectHistoryService->fetchProjectHistory($projectId);
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkProjectJoinedStatus($projectId, $userEmail)
    {
        try {
            return $this->projectMemberManagementService->checkProjectJoinedStatus($projectId, $userEmail);
        } catch (Exception $e) {
            return false;
        }
    }

    public function joinProject($projectId, $userEmail)
    {
        try {
            return $this->projectMemberManagementService->joinProject($projectId, $userEmail);
        } catch (Exception $e) {
            return false;
        }
    }

    public function unJoinProject($projectId, $userEmail)
    {
        try {
            return $this->projectMemberManagementService->unJoinProject($projectId, $userEmail);
        } catch (Exception $e) {
            return false;
        }
    }
}
