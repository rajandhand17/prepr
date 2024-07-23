<?php

namespace App\Services\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePath;
use SebastianBergmann\ObjectEnumerator\Exception;

class ChallengePathReportService
{
    public function getChallengePathMemberProgress(ChallengePath $challengePath): false|array
    {
        try {
            /**
             * LAZY LOADING.
             */
            $challengePath->load('challengePathProgress')->loadCount('challengePathProgress');

            /**
             * ALL PROGRESS.
             */
            $allProgress = $challengePath->challengePathProgress()->get();

            /**
             * FILTERING AND COUNTING.
             */
            $notStarted = $allProgress->where('status', '=', '0')->count();
            $inProgress = $allProgress->where('status', '=', '1')->count();
            $completed = $allProgress->where('status', '=', '2')->count();

            return [
                'not_started' => $notStarted,
                'in_progress' => $inProgress,
                'completed'   => $completed,
                'total'       => $challengePath->challenge_path_progress_count,
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
