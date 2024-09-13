<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ProjectIndustry as Industry;
use DB;
use Illuminate\Console\Command;

class ProjectIndustry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-industry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project industry table data to new db structure.';

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
            $this->info('Migrating old data for project industry table.');
            DB::beginTransaction();

            $project_industry = DB::connection('mysql2')->table('project_industry')->get();
            if ($project_industry->count() > 0) {
                foreach ($project_industry as $key => $single_industry) {
                    $check_project_industry = Industry::where('title', $single_industry->name)->first();
                    if ($check_project_industry) {
                        $newProjectIndustry = $check_project_industry;
                    } else {
                        $newProjectIndustry = new Industry();
                    }
                    $newProjectIndustry->id = $single_industry->id;
                    $newProjectIndustry->title = $single_industry->name;
                    $newProjectIndustry->fr_CA_title = $single_industry->fr_CA_name;
                    $newProjectIndustry->save();
                }
                DB::commit();
                $this->info('Migrating of old data for project industry table completed.');

                return;
            }
            DB::rollback();
            $this->error('No project industry found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
