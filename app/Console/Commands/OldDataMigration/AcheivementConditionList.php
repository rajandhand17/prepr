<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\AchievementConditionList as AchievementConditionLists;

class AcheivementConditionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:achievement-condition-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old achievement condition list table data to new db structure.';

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

            $this->info('Migrating old data for achievement condition list table.');
            DB::beginTransaction();

            $achievement_condition_list = DB::connection('mysql2')->table('achievement_condition_lists')->get();

            if($achievement_condition_list->count() > 0){
                foreach ($achievement_condition_list as $key => $single_acheivement_condition_list){
                    $achievement_condition_list_details=[
                        'title' => $single_acheivement_condition_list->condition_title,
                    ];
                     $check_achievement_condition_list = AchievementConditionLists::where($achievement_condition_list_details)->first();
                    if(!$check_achievement_condition_list){
                        AchievementConditionLists::create($achievement_condition_list_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for achievement condition list table completed.');
                return;
            }
            DB::rollback();
            $this->error('No achievement condition list found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
