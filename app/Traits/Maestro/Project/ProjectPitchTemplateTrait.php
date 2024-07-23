<?php

namespace App\Traits\Maestro\Project;

use App\Services\Maestro\ProjectPitchTemplateService;
use Exception;

trait ProjectPitchTemplateTrait
{
    private function findPitchTemplate($id)
    {
        try {
            $pitchTemplate = ProjectPitchTemplateService::findPitchTemplate($id);
            if ($pitchTemplate) {
                return $pitchTemplate;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getLanguage()
    {
        try {
            $languages = ProjectPitchTemplateService::getLanguage();
            if ($languages) {
                return $languages;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getPitchTemplate()
    {
        try {
            $pitchTemplate = ProjectPitchTemplateService::getPitchTemplate();
            if ($pitchTemplate) {
                return $pitchTemplate;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getPitchSectionById($id)
    {
        try {
            $pitchSection = ProjectPitchTemplateService::getPitchSectionById($id);
            if ($pitchSection) {
                return $pitchSection;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getPitchTaskById($id)
    {
        try {
            $pitchTask = ProjectPitchTemplateService::getPitchTaskById($id);
            if ($pitchTask) {
                return $pitchTask;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function storePitchTemplate($request, $id, $moduleMode)
    {
        try {
            if (ProjectPitchTemplateService::storePitchTemplate($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function updatePitchTemplate($request, $id, $moduleMode)
    {
        try {
            if (ProjectPitchTemplateService::updatePitchTemplate($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deletePitchTemplate($pitchTemplate)
    {
        try {
            if (ProjectPitchTemplateService::deletePitchTemplate($pitchTemplate)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
