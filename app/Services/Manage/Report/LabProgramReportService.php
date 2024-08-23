<?php

namespace App\Services\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Models\LabProgram;

class LabProgramReportService
{
    public function getLabProgramMemberProgress(LabProgram $labProgram): array|false
    {
        try {
            $labProgram->load(['labProgramProgress'])->loadCount('labProgramProgress');

            $allLabPrograms = $labProgram->labProgramProgress()->get();

            $notStarted = $allLabPrograms->where('status', '=', '0')->count();
            $inProgress = $allLabPrograms->where('status', '=', '1')->count();
            $completed = $allLabPrograms->where('status', '=', '2')->count();

            return [
                'not_started' => $notStarted,
                'in_progress' => $inProgress,
                'completed'   => $completed,
                'total'       => $labProgram->lab_program_progress_count,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
