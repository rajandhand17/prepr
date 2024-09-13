<?php

namespace App\Console\Commands\EmailSummary;

use App\Http\Controllers\Api\EmailSummary\EmailSummaryController;
use App\Models\User;
use Illuminate\Console\Command;

class EmailSummaryMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-summary-report:monthly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command sends monthly report to subscribers via Email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with('userSetting')->get();
        if (!empty($users)) {
            foreach ($users as $userData) {
                // Check user has enable subscription or not and proceed further.
                if ($userData['userSetting']->email_subscription_notification == '1') {
                    EmailSummaryController::sendEmailSummary($userData, 'monthly');
                }
            }
        }
    }
}
