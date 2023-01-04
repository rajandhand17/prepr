<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Organisation as Organisations;

class Organisation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string 
     */
    protected $signature = 'migrate-old-data:organisations';

    /** 
     * The console command description. 
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old organisation table data to new db structure.';

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

            $this->info('Migrating old data for organisation table.');
            DB::beginTransaction();

            $organisations = DB::connection('mysql2')->table('organisations')->get();
            if($organisations->count() > 0){
                
                foreach ($organisations as $key => $single_organisation){
                   $organisation_details=[
                        'user_id' => $single_organisation->user_id,
                        'name' => $single_organisation->name,
                        'slug' => $single_organisation->slug,
                        'vanity_slug' => $single_organisation->vanity_slug,
                        'description' => $single_organisation->description,
                        'cover_image' => $single_organisation->cover_image,
                        'profile_image' => $single_organisation->profile_image,
                        'about' => $single_organisation->about,
                        'category' => $single_organisation->category,
                        'latitude' => $single_organisation->latitude,
                        'longitude' => $single_organisation->longitude,
                        'address' => $single_organisation->address,
                        'vanity_link' => $single_organisation->vanity_link,
                        'status' => $single_organisation->status,
                        'labs_limit' => $single_organisation->labs_limit,
                        'challenges_limit' => $single_organisation->challenges_limit,
                    ];
                    $check_organisation = Organisations::where($organisation_details)->first();
                    if(!$check_organisation){
                        Organisations::create($organisation_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for organisation table completed.');
                return;
            }
            DB::rollback();
            $this->error('No organisation found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
