<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ProjectType as Type;

class ProjectType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project_type';

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

            $this->info('Migrating Old Data for Project type table.');
            DB::beginTransaction();

            $project_type = DB::connection('mysql2')->table('project_type')->get();
            if($project_type->count() > 0){
                
                foreach ($project_type as $key => $single_type){
                   $project_type_details=[
                        'name' => $single_type->name,
                        'fr_CA_name' => $single_type->fr_CA_name,
                    ];
                    $check_project_type = Type::where($project_type_details)->first();
                    if(!$check_project_type){
                        Type::create($project_type_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for Project Type table completed.');
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
