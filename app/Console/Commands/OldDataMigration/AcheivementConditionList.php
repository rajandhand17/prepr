<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\AchievementConditionList as AchievementConditionLists;
use DB;
use Illuminate\Console\Command;

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

            if ($achievement_condition_list->count() > 0) {
                foreach ($achievement_condition_list as $key => $single_acheivement_condition_list) {
                    $check_achievement_condition_list = AchievementConditionLists::where('title', $single_acheivement_condition_list->condition_title)->first();

                    if ($check_achievement_condition_list) {
                        $newCreated = $check_achievement_condition_list;
                    } else {
                        $newCreated = new AchievementConditionLists();
                    }
                    $newCreated->id = $single_acheivement_condition_list->id;
                    $newCreated->title = $single_acheivement_condition_list->condition_title;
                    $newCreated->save();
                }
                DB::commit();
                $this->info('Migrating of old data for achievement condition list table completed.');

                return;
            }
            DB::rollback();
            $this->error('No achievement condition list found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
