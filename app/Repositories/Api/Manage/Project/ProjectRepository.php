<?php

namespace App\Repositories\Api\Manage\Project;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ProjectPitchService;
use App\Services\Manage\ProjectService;
use Exception;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectInterface
{
    private $projectService;
    private $challengeService;
    private $labService;
    private $projectPitchService;

    public function __construct(ProjectService $projectService, ChallengeService $challengeService, LabService $labService, ProjectPitchService $projectPitchService)
    {
        $this->projectService = $projectService;
        $this->challengeService = $challengeService;
        $this->labService = $labService;
        $this->projectPitchService = $projectPitchService;
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
            $createProject = DB::transaction(function () use ($request, $uploadedCoverMedia) {
                $createProject = $this->projectService->createProject($request, $uploadedCoverMedia);

                return [
                    'createProject' => $createProject,
                ];
            });
            if ($createProject['createProject']) {
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

    public function getProjectBasedOnUUID($UUID)
    {
        try {
            return $this->projectService->getProjectBasedOnUUID($UUID);
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

    public function createProjectPitchTask($projectId, $request)
    {
        try {

            $createProjectPitchTaskAnswer = DB::transaction(function () use ($projectId, $request) {
                $createProjectPitchTaskAnswer = $this->projectPitchService->createProjectPitchTaskAnswer($projectId, $request);

                return $createProjectPitchTaskAnswer;
            });

            if ($createProjectPitchTaskAnswer) {
                DB::commit();

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
