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
            DB::statement("ALTER TABLE `lab_programs` CHANGE `category_id` `category_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `duration_id` `duration_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `level_id` `level_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `challenge_paths` CHANGE `category_id` `category_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `duration_id` `duration_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `level_id` `level_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `resource_modules` CHANGE `duration_id` `duration_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `level_id` `level_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `resource_collections` CHANGE `duration` `duration` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `level` `level` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `resource_groups` CHANGE `duration` `duration` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `level` `level` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `challenges` ADD `total_share` INT NULL DEFAULT NULL AFTER `views_count`;");
            DB::statement("ALTER TABLE `projects` ADD `total_share` INT NULL DEFAULT NULL AFTER `views_count`;");
            DB::statement("ALTER TABLE `projects` CHANGE `media_type` `media_type` ENUM('0','1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 -> Image, 1-> embedded, Type of media defining of media, 2-> Video', CHANGE `challenge_id` `challenge_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `project_pitch_values` CHANGE `pitch_template_id` `pitch_template_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `project_pitch_id` `project_pitch_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `project_task_values` CHANGE `task_template_id` `task_template_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `project_task_id` `project_task_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `challenge_templates` CHANGE `category_id` `category_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `duration_id` `duration_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `level_id` `level_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            DB::statement("ALTER TABLE `challenge_path_templates` CHANGE `category_id` `category_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `duration_id` `duration_id` BIGINT UNSIGNED NULL DEFAULT NULL, CHANGE `level_id` `level_id` BIGINT UNSIGNED NULL DEFAULT NULL;");
            Schema::enableForeignKeyConstraints();
            $this->info('Sql command executed successfully.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
