<?php

namespace App\Traits\Maestro\Project;

use App\Services\Maestro\ProjectIndustryService;
use Exception;

trait ProjectIndustryTrait
{
    private function getLanguage()
    {
        try {
            $languages = ProjectIndustryService::getLanguage();
            if ($languages) {
                return $languages;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getProjectIndustry()
    {
        try {
            $ProjectIndustry = ProjectIndustryService::getProjectIndustry();
            if ($ProjectIndustry) {
                return $ProjectIndustry;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getProjectIndustryStatus()
    {
        try {
            $status = ProjectIndustryService::getProjectIndustryStatus();
            if ($status) {
                return $status;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function storeUpdateProjectIndustry($request, $id, $moduleMode)
    {
        try {
            if (ProjectIndustryService::storeUpdateProjectIndustry($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function findProjectIndustry($id)
    {
        try {
            $ProjectIndustry = ProjectIndustryService::findProjectIndustry($id);
            if ($ProjectIndustry) {
                return $ProjectIndustry;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteProjectIndustry($ProjectIndustry)
    {
        try {
            if (ProjectIndustryService::deleteProjectIndustry($ProjectIndustry)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
