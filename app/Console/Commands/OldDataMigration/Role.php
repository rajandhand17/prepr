<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Role as Roles;

class Role extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old role table data to new db structure.';

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

            $this->info('Migrating old data for role table.');
            DB::beginTransaction();

            $roles = DB::connection('mysql2')->table('roles')->get();
            if($roles->count() > 0){
                
                foreach ($roles as $key => $single_roles){
                   $role_details=[
                        'name' => $single_roles->name,
                        'display_name' => $single_roles->display_name,
                    ];
                    $check_role = Roles::where($role_details)->first();
                    if(!$check_role){
                        Roles::create($role_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for roles table completed.');
                return;
            }
            DB::rollback();
            $this->error('No roles found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
