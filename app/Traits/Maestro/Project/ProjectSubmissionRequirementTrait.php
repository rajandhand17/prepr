<?php

namespace App\Traits\Maestro\Project;

use App\Services\Maestro\ProjectSubmissionRequirementService;
use Exception;

trait ProjectSubmissionRequirementTrait
{
    private function getSubmissionRequirement()
    {
        try {
            $submissionRequirement = ProjectSubmissionRequirementService::getSubmissionRequirement();
            if ($submissionRequirement) {
                return $submissionRequirement;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getSubmissionRequirementStatus()
    {
        try {
            $status = ProjectSubmissionRequirementService::getSubmissionRequirementStatus();
            if ($status) {
                return $status;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function storeUpdateSubmissionRequirement($request, $id, $moduleMode)
    {
        try {
            if (ProjectSubmissionRequirementService::storeUpdateSubmissionRequirement($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function findSubmissionRequirement($id)
    {
        try {
            $submissionRequirement = ProjectSubmissionRequirementService::findSubmissionRequirement($id);
            if ($submissionRequirement) {
                return $submissionRequirement;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteSubmissionRequirement($submissionRequirement)
    {
        try {
            if (ProjectSubmissionRequirementService::deleteSubmissionRequirement($submissionRequirement)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
