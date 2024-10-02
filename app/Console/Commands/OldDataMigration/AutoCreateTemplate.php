<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\AutoCreateTemplate as AutoCreateTemplateTable;
use Carbon\Carbon;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;

class AutoCreateTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:auto-create-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate data from legacy db table auto_create_template to learnlab auto_create_template_table.';

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
            $this->info('Started Migration from legacy to learnlab db for Auto-create template data');
            DB::beginTransaction();

            $autoCreateTemplateData = DB::connection('mysql2')->table('auto_create_templates')->get();
            if(!empty($autoCreateTemplateData)){
                foreach($autoCreateTemplateData as $autoCreateTemplate){
                    $roleType = '';
                    if(!empty($autoCreateTemplate->role_type)){
                        switch ($autoCreateTemplate->role_type) {
                            case 'free_organisation_manager':
                                $roleType = 'organization_manager';
                              break;
                            case 'organisation_manager':
                                $roleType = 'organization_owner';
                              break;
                            case 'org_lab_manager':
                                $roleType = 'lab_manager';
                              break;
                            case 'org_challenge_manager':
                                $roleType = 'challenge_manager';
                              break;
                            case 'org_resource_manager':
                                $roleType = 'resource_manager';
                              break;
                            case 'user':
                                $roleType = 'user';
                              break;
                            default:
                                $roleType = $autoCreateTemplate->role_type;
                        }
                    }
                    if(!empty($roleType)){
                        $data = [
                            'language'            => $autoCreateTemplate->language,
                            'role_type'           => $roleType,
                            'role_user_type'      => $autoCreateTemplate->role_user_type,
                            'lab_id'              => $autoCreateTemplate->lab_id,
                            'challenge_id'        => $autoCreateTemplate->challenge_id,
                            'project_id'          => $autoCreateTemplate->project_id,
                            'lab_group_id'        => $autoCreateTemplate->lab_group_id,
                            'challenge_group_id'  => $autoCreateTemplate->challenge_group_id,
                            'invite_labs'         => $autoCreateTemplate->invite_labs,
                            'invite_challenges'   => $autoCreateTemplate->invite_challenges,
                        ];
    
                        AutoCreateTemplateTable::create($data);
                    }
                }
            }
            DB::commit();
            $this->info('Completed Migration from legacy to learnlab db for auto-create template data');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());
        }
    }
}