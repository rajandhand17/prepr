<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category as Categories;
use DB;
use Illuminate\Console\Command;

class Category extends Command
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
            $this->info('Migrating old data for categories table.');
            DB::beginTransaction();

            $categories = DB::connection('mysql2')->table('categories')->whereNull('deleted_at')->get();

            if ($categories->count() > 0) {
                foreach ($categories as $key => $single_category) {
                    $checkCategory = Categories::where('title', $single_category->name)->first();

                    if ($checkCategory) {
                        $newCategory = $checkCategory;
                    } else {
                        $newCategory = new Categories();
                    }

                    $newCategory->id = $single_category->id;
                    $newCategory->title = $single_category->name;
                    $newCategory->fr_CA_title = $single_category->fr_CA_name;
                    $newCategory->components = $single_category->components;
                    $newCategory->parent_id = $single_category->parent_id;
                    $newCategory->created_at = $single_category->created_at;
                    $newCategory->updated_at = $single_category->updated_at;
                    $newCategory->save();
                }
                DB::commit();
                $this->info('Migrating of old data for categories table completed.');

                return;
            }
            DB::rollback();
            $this->error('No categories found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
