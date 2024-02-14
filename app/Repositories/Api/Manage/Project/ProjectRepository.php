<?php

namespace App\Repositories\Api\Manage\Project;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ProjectAdditionalInfoService;
use App\Services\Manage\ProjectExternalLinksService;
use App\Services\Manage\ProjectFileService;
use App\Services\Manage\ProjectMemberManagementService;
use App\Services\Manage\ProjectPitchService;
use App\Services\Manage\ProjectService;
use App\Services\Public\ProjectSocialActivitiesService;
use Exception;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectInterface
{
    private $projectService;
    private $challengeService;
    private $labService;
    private $projectPitchService;
    private $projectFileService;
    private $projectExternalLinksService;
    private $projectAdditionalInfoService;
    private $projectMemberManagementService;
    private $projectSocialActivitiesService;

    public function __construct(ProjectService $projectService, ChallengeService $challengeService, LabService $labService, ProjectPitchService $projectPitchService, ProjectFileService $projectFileService, ProjectExternalLinksService $projectExternalLinksService, ProjectAdditionalInfoService $projectAdditionalInfoService, ProjectMemberManagementService $projectMemberManagementService, ProjectSocialActivitiesService $projectSocialActivitiesService)
    {
        $this->projectService = $projectService;
        $this->challengeService = $challengeService;
        $this->labService = $labService;
        $this->projectPitchService = $projectPitchService;
        $this->projectFileService = $projectFileService;
        $this->projectExternalLinksService = $projectExternalLinksService;
        $this->projectAdditionalInfoService = $projectAdditionalInfoService;
        $this->projectMemberManagementService = $projectMemberManagementService;
        $this->projectSocialActivitiesService = $projectSocialActivitiesService;
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

    public function getInvitedProjectIds($userData)
    {
        try {
            return $this->projectMemberManagementService->getInvitedProjectIds($userData);
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
            return false;
        }
    }

    public function getProjectChallenges($request)
    {
        try {
            return $this->challengeService->getProjectChallenges($request);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProjectLabs($request, $challengeId)
    {
        try {
            return $this->labService->getProjectLabs($request, $challengeId);
        } catch (Exception $e) {
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

    public function getProjectBasedOnUUID($uuid)
    {
        try {
            return $this->projectService->getProjectBasedOnUUID($uuid);
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
            DB::beginTransaction();

            $submitProject = $this->projectService->submitProject($projectData);
            if ($submitProject == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
