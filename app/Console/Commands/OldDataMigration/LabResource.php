<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\LabResources;

class LabResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:lab-resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old lab resource table data to new db structure.';

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

            $this->info('Migrating old data for lab resource table.');
            DB::beginTransaction();

            $lab_resources = DB::connection('mysql2')->table('lab_resources')->get();
            if($lab_resources->count() > 0){
                foreach ($lab_resources as $key => $single_lab_resources){
                   $lab_resources_details=[
                        'lab_id' => $single_lab_resources->lab_id,
                        'resources_id' => $single_lab_resources->resources_id,
                        "collection_id"=> $single_lab_resources->collection_id,
                        "group_id"=>$single_lab_resources->group_id,
                        "status"=>$single_lab_resources->status,
                    ];
                    
                    $check_lab_resources = LabResources::where($lab_resources_details)->first();
                    if(!$check_lab_resources){
                        LabResources::create($lab_resources_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for lab resources table completed.');
                return;
            }
            DB::rollback();
            $this->error('No lab resources found.');

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            $this->error('Something went wrong.');
            return;
        }
    }
}
