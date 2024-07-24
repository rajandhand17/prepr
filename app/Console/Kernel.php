<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('challenges:close-challenge-for-passed-dates')->everyMinute();
        $schedule->command('challenges:update-challenge-passed-winner-date')->everyMinute();
        $schedule->command('member-manger:send-email')->everyMinute();
        $schedule->command('chargebee-subscription:daily-chronicle-accessed-non-accessed-data')->everyMinute();
        $schedule->command('solr:sync')->daily();
        $schedule->command('email-summary-report:monthly-report')->monthly();
        $schedule->command('email-summary-report:weekly-report')->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
