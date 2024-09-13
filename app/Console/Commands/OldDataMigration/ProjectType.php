<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ProjectType as Type;
use DB;
use Illuminate\Console\Command;

class ProjectType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project type table data to new db structure.';

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
            $this->info('Migrating old data for project type table.');
            DB::beginTransaction();

            $project_type = DB::connection('mysql2')->table('project_type')->get();
            if ($project_type->count() > 0) {
                foreach ($project_type as $key => $single_type) {
                    $check_project_type = Type::where('title', $single_type->name)->first();
                    if ($check_project_type) {
                        $newProjectType = $check_project_type;
                    } else {
                        $newProjectType = new Type();
                    }
                    $newProjectType->id = $single_type->id;
                    $newProjectType->title = $single_type->name;
                    $newProjectType->fr_CA_title = $single_type->fr_CA_name;
                    $newProjectType->save();
                }
                DB::commit();
                $this->info('Migrating of old data for project type table completed.');

                return;
            }
            DB::rollback();
            $this->error('No project type found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
