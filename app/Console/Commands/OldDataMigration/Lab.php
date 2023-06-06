<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Lab as Labs;

class Lab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:lab';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old host table data to new db structure.';

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

            $this->info('Migrating old data for lab table.');
            DB::beginTransaction();

            $labs = DB::connection('mysql2')->table('labs')->get();
            if($labs->count() > 0){
                foreach ($labs as $key => $single_lab){
                   $lab_details=[
                    'language'=> $single_lab->language,
                    'slug'=> $single_lab->slug,
                    'user_id'=> $single_lab->user_id,
                    'organisation'=> $single_lab->organisation,
                    'title'=> $single_lab->title,
                    'verification'=> $single_lab->verification,
                    'description'=> $single_lab->description,
                    'category'=> $single_lab->category,
                    'privacy'=> $single_lab->privacy,
                    'mediaType'=> $single_lab->mediaType,
                    'image'=> $single_lab->image,
                    'member'=> $single_lab->member,
                    'member_type'=> $single_lab->member_type,
                    'latitute'=> $single_lab->latitute,
                    'longitude'=> $single_lab->longitude,
                    'address'=> $single_lab->address,
                    'city'=> $single_lab->city,
                    'country'=> $single_lab->country,
                    'challnges'=> $single_lab->challnges,
                    'lab_skills'=> $single_lab->lab_skills,
                    'tag'=> $single_lab->tag,
                    'status'=> $single_lab->status,
                    'phone'=> $single_lab->phone,
                    'company'=> $single_lab->company,
                    'email'=> $single_lab->email,
                    'website'=> $single_lab->website,
                    'facebook'=> $single_lab->facebook,
                    'linked'=> $single_lab->linked, 
                    'twitter'=> $single_lab->twitter, 
                    'total_share'=> $single_lab->total_share, 
                    'user_count'=> $single_lab->user_count, 
                    'is_auto_created'=>$single_lab->is_auto_created,
                    'res_sequence'=>$single_lab->res_sequence,
                    'cha_sequence'=>$single_lab->cha_sequence,
                    'enable_achievement'=>$single_lab->enable_achievement,
                    'skill_groups'=>$single_lab->skill_groups,
                    'skill_stacks'=>$single_lab->skill_stacks,
                    ];
                    $check_labs = Labs::where($lab_details)->first();
                    if(!$check_labs){
                       $records= Labs::create($lab_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for labs table completed.');
                return;
            }
            DB::rollback();
            $this->error('No Labs found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
