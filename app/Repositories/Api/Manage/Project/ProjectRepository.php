<?php

namespace App\Repositories\Api\Manage\Project;

use App\Services\Manage\ProjectService;
use Exception;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectInterface
{
    private $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
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
}
