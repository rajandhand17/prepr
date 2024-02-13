<?php

namespace App\Services\Public;

use App\Models\Project;
use Exception;

class ProjectService
{
    public function getProjectBasedOnSlug($slug)
    {
        try {
            $fetchProject = Project::where('slug', $slug)->first();
            if ($fetchProject) {
                return $fetchProject;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
