<?php

namespace App\Repositories\Api\Project;

use App\Services\AchievementService;
use App\Services\ChallengeAssessmentUserService;
use App\Services\Manage\ChallengeAchievementService;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\ChallengeService;
use App\Services\ProjectAdditionalInfoService;
use App\Services\ProjectExternalLinksService;
use App\Services\ProjectFileService;
use App\Services\ProjectMemberManagementService;
use App\Services\ProjectPitchService;
use App\Services\ProjectService;
use App\Services\ProjectSkillsService;
use App\Services\ProjectSocialActivitiesService;
use Exception;
use Illuminate\Support\Facades\DB;

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

    public function __construct(ProjectService $projectService, ChallengeService $challengeService, ProjectPitchService $projectPitchService, ProjectFileService $projectFileService, ProjectExternalLinksService $projectExternalLinksService, ProjectAdditionalInfoService $projectAdditionalInfoService, ProjectMemberManagementService $projectMemberManagementService, ProjectSocialActivitiesService $projectSocialActivitiesService, ChallengeAchievementService $challengeAchievementService, AchievementService $achievementService, ChallengeAssessmentService $challengeAssessmentService, ChallengeAssessmentUserService $challengeAssessmentUserService, ProjectSkillsService $projectSkillsService)
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
            $inviteType = '1';
            $inviteStatus = '1';
            $emailStatus = '1';
            $accessLevel = '2';

            $createProject = DB::transaction(function () use ($request, $uploadedCoverMedia, $userId, $userEmail, $inviteType, $inviteStatus, $emailStatus, $accessLevel) {
                $createProject = $this->projectService->createProject($request, $uploadedCoverMedia);
                $createProjectMember = $this->projectMemberManagementService->feedParticipatesData($createProject->id, $userId, $userEmail, $inviteType, $inviteStatus, $emailStatus, $accessLevel);

                return [
                    'createProject'         => $createProject,
                    'createProjectMember'   => $createProjectMember,
                ];
            });
            if ($createProject['createProject'] && $createProject['createProjectMember']) {
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

                return [
                    'submitProject'  => $submitProject,
                    'addAchievement' => $addAchievement,
                ];
            });

            if ($submitProject['submitProject'] &&
                $submitProject['addAchievement']) {
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

            if ($addUpdateProjectSkillsRecruitingStatus['addUpdateProjectSkills'] &&
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
}
