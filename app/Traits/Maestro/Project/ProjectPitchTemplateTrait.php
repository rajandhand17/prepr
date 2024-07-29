<?php

namespace App\Traits\Maestro\Project;

use App\Services\Maestro\ChallengePitchService;
use App\Services\Maestro\ChallengeTaskService;
use App\Services\Maestro\ProjectPitchTemplateService;
use Exception;
use Illuminate\Support\Facades\DB;

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
            DB::rollBack();

            return false;
        }
    }
    private function storeUpdatePitchTemplate($request, $id, $moduleMode)
    {
        try {
            $createPitchTemplate = DB::transaction(function () use ($request, $moduleMode, $id) {
                $pitchTemplate = ProjectPitchTemplateService::storeUpdatePitchTemplate($request, $id, $moduleMode);
                $pitchSection = ChallengePitchService::saveChallengePitch($request, $pitchTemplate);
                $pitchTask = ChallengeTaskService::saveChallengeTask($request, $pitchTemplate);

                return [
                    'pitchTemplate' => $pitchTemplate,
                    'pitchSection'  => $pitchSection,
                    'pitchTask'     => $pitchTask,
                ];
            });

            if ($createPitchTemplate['pitchTemplate'] && $createPitchTemplate['pitchSection'] && $createPitchTemplate['pitchTask']) {
                DB::commit();

                return $createPitchTemplate['pitchTemplate'];
            }
            DB::rollBack();
            return false;
        } catch (Exception $e) {
            DB::rollBack();

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
