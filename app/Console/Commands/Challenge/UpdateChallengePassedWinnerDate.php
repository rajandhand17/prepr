<?php

namespace App\Console\Commands\Challenge;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateChallengePassedWinnerDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenges:update-challenge-passed-winner-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Challenge Winner Selection Status for passed date(deadlines)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $challengeDatas = Challenge::where('allow_winner_change', '0')->whereNotNull('winner_select_date')->get();
            if ($challengeDatas->isNotEmpty()) {
                foreach ($challengeDatas as $challengeData) {
                    $challengeDateTime = $challengeData->winner_select_date;
                    $currentDateTime = Carbon::now();
                    $checkHoursDifference = $currentDateTime->diffInHours($challengeDateTime);
                    if ($checkHoursDifference >= 24) {
                        $challengeData->update(['allow_winner_change' => '1']);
                        $this->info('This challenge id has been updated:- '.$challengeData->id);
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error('Allow Challenge Winner selection status not updated');
        }
    }
}
