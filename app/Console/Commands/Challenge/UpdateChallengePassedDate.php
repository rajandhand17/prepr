<?php

namespace App\Console\Commands\Challenge;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class UpdateChallengePassedDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenges:close-challenge-for-passed-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Challenge Status for passed date(deadlines)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $currentDate = date('Y-m-d H:i:s'); // Getting current date and time
            $challengeDatas = Challenge::with('challenge_timelines')->where(['status' => '1', 'is_open' => '0'])->get();

            if ($challengeDatas->isNotEmpty()) {
                foreach ($challengeDatas as $challengeData) {
                    $challengeTimeline = $challengeData->challenge_timelines()->first();

                    if ($challengeTimeline) {
                        if ($challengeTimeline->timeline_type === '0' && $challengeTimeline->flexible_expire_deadline !== '1969-12-31 00:00:00' && !isEmpty($challengeTimeline->flexible_expire_deadline) && $challengeTimeline->flexible_expire_deadline < $currentDate) {
                            $challengeData->update(['is_open' => '1']);
                            $this->info('This challenge id has been updated:- '.$challengeData->id);
                        } elseif ($challengeTimeline->timeline_type === '1' && $challengeTimeline->submission_deadline_date < $currentDate) {
                            $challengeData->update(['is_open' => '1']);
                            $this->info('This challenge id has been updated:- '.$challengeData->id);
                        }
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error('Challenge Status not updated');
        }
    }
}
