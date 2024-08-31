<?php

namespace App\Console\Commands\SQL;

use App\Helpers\UtilityHelper;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            Schema::disableForeignKeyConstraints();
            DB::table('projects')->truncate();
            DB::table('project_additional_info')->truncate();
            DB::table('project_skills')->truncate();
            DB::table('project_member_management')->truncate();
            DB::table('project_external_links')->truncate();
            DB::table('project_templates')->truncate();
            DB::table('project_pitch_values')->truncate();
            DB::table('project_task_values')->truncate();
            Schema::enableForeignKeyConstraints();
            $this->info('Sql command executed successfully.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
