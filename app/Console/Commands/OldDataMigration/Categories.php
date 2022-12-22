<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Category;

class Categories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old categories table data to new db structure.';

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

            $this->info('Migrating Old Data for categories table.');
            DB::beginTransaction();

            $categories = DB::connection('mysql2')->table('categories')->get();

            if($categories->count() > 0){
                foreach ($categories as $key => $single_category){
                    $category_details=[
                        'name' => $single_category->name,
                        'fr_CA_name' => $single_category->fr_CA_name,
                        'components' => $single_category->components,
                        'parent_id' => $single_category->parent_id
                    ];
                    $checkCategory = Category::where($category_details)->first();
                    if(!$checkCategory){
                        Category::create($category_details);
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
