<?php

namespace App\Repositories\Api\Manage\Report\Lab;

use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabProgram;
use App\Services\Manage\Report\LabProgramReportService;
use App\Services\Manage\Report\LabReportService;

class LabReportRepository implements LabReportInterface
{
    public function __construct(
        protected LabReportService $labReportService,
        protected LabProgramReportService $labProgramReportService
    ) {
    }

    public function getLabEngagement(Lab $lab): false|array
    {
        try {
            return $this->labReportService->getLabEngagement($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function labMemberProgress(Lab $lab): array|false
    {
        try {
            return $this->labReportService->labMemberProgress($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function labMemberActivity(Lab $lab): array|false
    {
        try {
            return $this->labReportService->labMemberActivity($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedChallenges(Lab $lab): false|array
    {
        try {
            return $this->labReportService->getPaginatedChallenges($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceModules(Lab $lab): false|array
    {
        try {
            return $this->labReportService->getPaginatedResourceModules($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceCollections(Lab $lab): false|array
    {
        try {
            return $this->labReportService->getPaginatedResourceCollections($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceGroups(Lab $lab): false|array
    {
        try {
            return $this->labReportService->getPaginatedResourceGroups($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedChallengePaths(Lab $lab): false|array
    {
        try {
            return $this->labReportService->getPaginatedChallengePaths($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedAchievements(Lab $lab): false|array
    {
        try {
            return $this->labReportService->getPaginatedAchievements($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedMembers($lab): false|array
    {
        try {
            return $this->labReportService->getPaginatedMembers($lab);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getLabProgramMemberProgress(LabProgram $labProgram): array|false
    {
        try {
            return $this->labProgramReportService->getLabProgramMemberProgress($labProgram);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
