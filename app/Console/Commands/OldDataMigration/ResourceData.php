<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ResourceData as ResourceDatas;

class ResourceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-datas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old resource datas table data to new db structure.';

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

            $this->info('Migrating old data for resource datas table.');
            DB::beginTransaction();

            $resource_datas = DB::connection('mysql2')->table('admin_challenge_resource_datas')->get();
            if($resource_datas->count() > 0){
                
                foreach ($resource_datas as $key => $single_resource_datas){
                   $resource_datas_details=[
                        'admin_challenge_id' => $single_resource_datas->admin_challenge_id,
                        'resource_datas_id' => $single_resource_datas->resource_datas_id,
                    ];
                    $check_pitches_details = ResourceDatas::where($resource_datas_details)->first();
                    if(!$check_pitches_details){
                        ResourceDatas::create($resource_datas_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for resource datas table completed.');
                return; 
            }
            DB::rollback();
            $this->error('No resource datas found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
