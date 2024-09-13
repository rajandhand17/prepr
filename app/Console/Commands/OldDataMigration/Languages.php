<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Language;
use DB;
use Illuminate\Console\Command;

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

            if ($languages->count() > 0) {
                foreach ($languages as $key => $single_language) {
                    $checkLanguage = Language::where(['name' => $single_language->lang_name, 'iso' => $single_language->lang_iso])->first();
                    if ($checkLanguage) {
                        $newLanguage = $checkLanguage;
                    } else {
                        $newLanguage = new Language();
                    }
                    $newLanguage->id = $single_language->id;
                    $newLanguage->name = $single_language->lang_name;
                    $newLanguage->iso = $single_language->lang_iso;
                    $newLanguage->status = $single_language->status;
                    $newLanguage->is_imported = $single_language->is_imported;
                    $newLanguage->save();
                }
                DB::commit();
                $this->info('Migrating of old data for languages table completed.');

                return;
            }
            DB::rollback();
            $this->error('No languages found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
