<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ProjectStage;
use DB;
use Illuminate\Console\Command;

class ProjectStages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-stages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project Stages table data to new db structure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for project stages table.');
            DB::beginTransaction();

            $project_stages = DB::connection('mysql2')->table('project_stage')->get();
            if ($project_stages->count() > 0) {
                foreach ($project_stages as $key => $single_stages) {
                    $check_project_stages = ProjectStage::where('title', $single_stages->name)->first();
                    if ($check_project_stages) {
                        $newProjectStage = $check_project_stages;
                    } else {
                        $newProjectStage = new ProjectStage();
                    }
                    $newProjectStage->id = $single_stages->id;
                    $newProjectStage->title = $single_stages->name;
                    $newProjectStage->fr_CA_title = $single_stages->fr_CA_name;
                    $newProjectStage->save();
                }
                DB::commit();
                $this->info('Migrating of old data for project stages table completed.');

                return;
            }
            DB::rollback();
            $this->error('No project stage found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
