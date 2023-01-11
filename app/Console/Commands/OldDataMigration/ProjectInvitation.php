<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ProjectInvitation as ProjectInvitations;

class ProjectInvitation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-invitation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project invitation table data to new db structure.';

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

            $this->info('Migrating old data for project invitation table.');
            DB::beginTransaction();

            $project_stages = DB::connection('mysql2')->table('project_invitations')->get();
            if($project_stages->count() > 0){
                
                foreach ($project_stages as $key => $single_stages){
                   $project_invitations_details=[
                        'type' => $single_stages->type,
                        'project_id' => $single_stages->project_id,
                        'email' => $single_stages->email,
                        'status' => $single_stages->status,
                    ];
                    $check_project_stages = ProjectInvitations::where($project_invitations_details)->first();
                    if(!$check_project_stages){
                        ProjectInvitations::create($project_invitations_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for project invitations table completed.');
                return;
            }
            DB::rollback();
            $this->error('No project invitations found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
