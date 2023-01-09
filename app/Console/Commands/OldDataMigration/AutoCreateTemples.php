<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\AutoCreateTemplate;

class AutoCreateTemples extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:auto-create-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old auto-create-templates table data to new db structure.';

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

            $this->info('Migrating old data for auto-create-templates table.');
            DB::beginTransaction();

            $auto_create_template = DB::connection('mysql2')->table('auto_create_templates')->get();
             
            if($auto_create_template->count() > 0){
                foreach ($auto_create_template as $key => $single_auto_create_template){
                    $achievement_auto_create_templates=[
                        'language' => $single_auto_create_template->language,
                        'role_type' => $single_auto_create_template->role_type,
                        'role_user_type' => $single_auto_create_template->role_user_type,
                        'lab_id' => $single_auto_create_template->lab_id,
                        'challenge_id' => $single_auto_create_template->challenge_id,
                        'project_id' => $single_auto_create_template->project_id,
                        'lab_group_id' => $single_auto_create_template->lab_group_id,
                        'challenge_group_id' => $single_auto_create_template->challenge_group_id,
                        'invite_labs' => $single_auto_create_template->invite_labs,
                        'invite_challenges' => $single_auto_create_template->invite_challenges,
                    ];
                     $check_achievement_condition_list = AutoCreateTemplate::where($achievement_auto_create_templates)->first();
                    if(!$check_achievement_condition_list){
                        AutoCreateTemplate::create($achievement_auto_create_templates);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for achievement auto create templates completed.');
                return;
            }
            DB::rollback();
            $this->error('No achievement auto create templates found.');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
