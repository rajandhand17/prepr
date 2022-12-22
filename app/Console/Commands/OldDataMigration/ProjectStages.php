<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ProjectStage;

class ProjectStages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project_Stages';

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

            $this->info('Migrating Old Data for Project Stages table.');
            DB::beginTransaction();

            $project_stages = DB::connection('mysql2')->table('project_Stage')->get();
            if($project_stages->count() > 0){
                
                foreach ($project_stages as $key => $single_stages){
                   $project_stages_details=[
                        'name' => $single_stages->name,
                        'fr_CA_name' => $single_stages->fr_CA_name,
                    ];
                    $check_project_stages = ProjectStage::where($project_stages_details)->first();
                    if(!$check_project_stages){
                        ProjectStage::create($project_stages_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for Project Stages table completed.');
                return;
            }
            DB::rollback();
            $this->error('No Project Type found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
