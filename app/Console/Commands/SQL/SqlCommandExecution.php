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
            DB::table('resource_modules')->truncate();
            DB::table('resource_module_details')->truncate();
            DB::table('resource_module_ratings')->truncate();
            DB::table('resource_module_skills_groups_stacks')->truncate();
            DB::table('resource_module_social_activities')->truncate();
            DB::table('resource_module_tags_groups')->truncate();
            DB::table('resource_module_type_modes')->truncate();
            DB::table('resource_module_visits')->truncate();
            DB::table('scorm')->truncate();
            DB::table('scorm_sco')->truncate();
            DB::table('scorm_sco_tracking')->truncate();
            Schema::enableForeignKeyConstraints();
            $this->info('Sql command executed successfully.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
