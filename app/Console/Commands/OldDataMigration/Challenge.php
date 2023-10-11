<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Organization;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Challenge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:challenge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old challanges table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for challenges table started.');
            $challenges = DB::connection('mysql2')->table('challanges')->limit(3)->get();
            if ($challenges->count() > 0) {
                foreach ($challenges as $key => $challenge) {
                    $checkUser = User::find($challenge->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $checkOrganization = Organization::find($challenge->organisation);
                    dd($checkUser, $checkOrganization);
                }
            }
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }
}
