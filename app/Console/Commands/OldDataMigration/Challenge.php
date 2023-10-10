<?php

namespace App\Console\Commands\OldDataMigration;

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
                    dd($challenge);
                }
            }
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }
}
