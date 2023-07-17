<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\ProjectStatus as Status;
use DB;
use Illuminate\Console\Command;

class ProjectStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project Status table data to new db structure.';

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
            $this->info('Migrating old data for project status table.');
            DB::beginTransaction();

            $project_status = DB::connection('mysql2')->table('project_status')->get();
            if ($project_status->count() > 0) {
                foreach ($project_status as $key => $single_status) {
                    $project_status_details = [
                        'id'          => $single_status->id,
                        'title'       => $single_status->name,
                        'fr_CA_title' => $single_status->fr_CA_name,
                    ];
                    $check_project_status = Status::where($project_status_details)->first();
                    if (!$check_project_status) {
                        Status::create($project_status_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for project status table completed.');

                return;
            }
            DB::rollback();
            $this->error('No project type found.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
