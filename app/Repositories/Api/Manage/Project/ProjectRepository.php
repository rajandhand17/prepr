<?php

namespace App\Repositories\Api\Manage\Project;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ProjectService;
use Exception;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectInterface
{
    private $projectService;
    private $challengeService;
    private $labService;

    public function __construct(ProjectService $projectService, ChallengeService $challengeService, LabService $labService)
    {
        $this->projectService = $projectService;
        $this->challengeService = $challengeService;
        $this->labService = $labService;
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

    public function checkSlug($slug)
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
}
