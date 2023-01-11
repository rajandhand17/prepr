<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\LabSocialLink as LabSocialLinks;

class LabSocialLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:lab-social-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old lab social link table data to new db structure.';

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

            $this->info('Migrating old data for lab social link table.');
            DB::beginTransaction();

            $lab_sociallink = DB::connection('mysql2')->table('lab_sociallink')->get();
            if($lab_sociallink->count() > 0){
                foreach ($lab_sociallink as $key => $single_lab_sociallink){
                   $lab_sociallink_details=[
                        'user_id' => $single_lab_sociallink->user_id,
                        'lab_id' => $single_lab_sociallink->lab_id,
                        "social_link_id"=> $single_lab_sociallink->social_link_id,
                       "link_url"=>$single_lab_sociallink->link_url,
                    ];
                    
                    $check_lab_sociallink = LabSocialLinks::where($lab_sociallink_details)->first();
                    if(!$check_lab_sociallink){
                        LabSocialLinks::create($lab_sociallink_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for lab Social Links table completed.');
                return;
            }
            DB::rollback();
            $this->error('No lab Social Links found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
