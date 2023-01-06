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
                foreach ($groups as $key => $single_category){
                    $category_details=[
                        'title' => $single_category->name,
                        'fr_CA_name' => $single_category->fr_CA_name,
                        'components' => $single_category->components,
                        'parent_id' => $single_category->parent_id
                    ];
                    $checkCategory = Groups::where($category_details)->first();
                    if(!$checkCategory){
                        Groups::create($category_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for categories table completed.');
                return;
            }
            DB::rollback();
            $this->error('No categories found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
