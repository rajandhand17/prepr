<?php

namespace App\Console\Commands\SQL;

use App\Helpers\UtilityHelper;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SqlCommandExecution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:sql-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command used to execute SQL command on demand';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::statement('TRUNCATE TABLE project_submission_requirements');
            $this->info('Sql command executed successfully.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
