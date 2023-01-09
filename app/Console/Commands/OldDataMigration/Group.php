<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Group as Groups;

class Group extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old group table data to new db structure.';

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

            $this->info('Migrating old data for group table.');
            DB::beginTransaction();

            $groups = DB::connection('mysql2')->table('groups')->get();

            if($groups->count() > 0){
                foreach ($groups as $key => $single_group){
                    $group_details=[
                        'title' => $single_group->title,
                        'description' => $single_group->description,
                        'organisation' => $single_group->organisation,
                        'category' => $single_group->category,
                        'type' => $single_group->type,
                        'challenge_id' => $single_group->challenge_id,
                        'lab_id' => $single_group->lab_id,
                        'resource_id' => $single_group->resource_id,
                        'collection_id' => $single_group->collection_id,
                        'user_id' => $single_group->user_id,
                        'group_image' => $single_group->group_image,
                        'privacy' => $single_group->privacy,
                        'status' => $single_group->status,
                        'challenge_privacy' => $single_group->challenge_privacy,
                        'privacy_project' => $single_group->privacy_project,
                        'published' => $single_group->published,
                        'prize' => $single_group->prize,
                        'points' => $single_group->points,
                        'trophy' => $single_group->trophy,
                        'is_auto_created' => $single_group->is_auto_created,
                    ];
                    $check_group = Groups::where($group_details)->first();
                    if(!$check_group){
                        Groups::create($group_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for group table completed.');
                return;
            }
            DB::rollback();
            $this->error('No group found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
