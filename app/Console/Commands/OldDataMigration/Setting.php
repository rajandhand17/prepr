<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Setting as Settings;

class Setting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old setting table data to new db structure.';

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
            $this->info('Migrating old data for setting table.');
            DB::beginTransaction();

            $settings = DB::connection('mysql2')->table('settings')->get();
            if($settings->count() > 0){
                
                foreach ($settings as $key => $single_settings){
                   $setting_details=[
                        'code' => $single_settings->code,
                        'type' => $single_settings->type,
                        'label' => $single_settings->label,
                        'value' => $single_settings->value,
                        'hidden' => $single_settings->hidden,
                    ];
                    $check_setting = Settings::where($setting_details)->first();
                    if(!$check_setting){
                        Settings::create($setting_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for settings table completed.');
                return;
            }
            DB::rollback();
            $this->error('No settings found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
