<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Language;

class Languages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old languages table data to new db structure.';

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

            $this->info('Migrating old data for languages table.');
            DB::beginTransaction();

            $languages = DB::connection('mysql2')->table('languages')->get();

            if($languages->count() > 0){
                foreach ($languages as $key => $single_language){
                    $language_details=[
                        'name' => $single_language->lang_name,
                        'iso' => $single_language->lang_iso,
                        'status' => $single_language->status,
                        'is_imported' => $single_language->is_imported
                    ];
                    $checkCategory = Language::where($language_details)->first();
                    if(!$checkCategory){
                        Language::create($language_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for languages table completed.');
                return;
            }
            DB::rollback();
            $this->error('No languages found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
