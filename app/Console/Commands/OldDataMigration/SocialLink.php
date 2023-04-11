<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\SocialLink as Link;

class SocialLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:social-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old social link table data to new db structure.';

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

            $this->info('Migrating old data for social link table.');
            DB::beginTransaction();

            $social_link = DB::connection('mysql2')->table('social_link')->get();
            if($social_link->count() > 0){

                foreach ($social_link as $key => $single_social_link){
                   $social_link_details=[
                        'name' => $single_social_link->link_name,
                        'icon' => $single_social_link->link_icon,
                    ];
                    $check_skills = Link::where($social_link_details)->first();
                    if(!$check_skills){
                        Link::create($social_link_details);
                    }

                }
                DB::commit();
                $this->info('Migrating of old data for social link table completed.');
                return;
            }
            DB::rollback();
            $this->error('No social link found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
