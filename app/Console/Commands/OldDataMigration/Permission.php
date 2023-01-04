<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Permission as Permissions;

class Permission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project permission table data to new db structure.';

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

            $this->info('Migrating old data for project permission table.');
            DB::beginTransaction();

            $permissions = DB::connection('mysql2')->table('permissions')->get();
            if($permissions->count() > 0){
                 
                foreach ($permissions as $key => $single_permission){
                    $project_permission_details=[
                        'name' => $single_permission->name,
                        'guard_name' => $single_permission->guard_name,
                        'order_by' => $single_permission->order_by,
                        'category' => $single_permission->category,
                    ];
                    $check_permission_industry = Permissions::where($project_permission_details)->first();
                    if(!$check_permission_industry){
                        Permissions::create($project_permission_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for project permission table completed.');
                return;
            }
            DB::rollback();
            $this->error('No project permission found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
