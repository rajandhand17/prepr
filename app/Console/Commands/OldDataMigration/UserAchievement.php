<?php

namespace App\Console\Commands\OldDataMigration;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
class UserAchievement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-achievement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users achievements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $this->info('Migrating old data for users table.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_achievements')->chunkById(1000, function ($userAchievement) {

                foreach ($userAchievement as $single_user_achievement) {
                    $achievement_type=null;
                    $checkUserAchievement =\App\Models\UserAchievement::where("id",$single_user_achievement->id)->first();
                    if($checkUserAchievement){
                        $userAchievement=$checkUserAchievement;
                    }else{
                        $userAchievement=new \App\Models\UserAchievement();
                    }
                    dd($single_user_achievement);
                    switch ($single_user_achievement->achievement_type) {
                        case 'lab':
                            $achievement_type = config('constants.user_achievement_type.lab');
                            break;
                        case 'labprogram':
                            $achievement_type = config('constants.user_achievement_type.lab_program');
                            break;
                        case 'challenge':
                            $achievement_type = config('constants.user_achievement_type.challenge');
                            break;
                        case 'challengepath':
                            $achievement_type = config('constants.user_achievement_type.challenge_path');
                            break;
                        case 'resourcegroup':
                            $achievement_type = config('constants.user_achievement_type.resource_group');
                            break;
                        case 'appreciationaward':
                            $achievement_type = config('constants.user_achievement_type.appreciation_award');
                            break;
                        case 'activityaward':
                            $achievement_type = config('constants.user_achievement_type.activity_award');
                            break;
                        case 'skillactivity':
                            $achievement_type = config('constants.user_achievement_type.skill_activity');
                            break;
                        case 'importedaward':
                            $achievement_type = config('constants.user_achievement_type.imported_award');
                            break;
                        case 'winneraward':
                            $achievement_type = config('constants.user_achievement_type.winner_award');
                            break;
                        case 'participationaward':
                            $achievement_type = config('constants.user_achievement_type.participation_award');
                            break;
                        default:
                            $achievement_type = null;
                    }

                    $userAchievement->id = $single_user_achievement->id;
                    $userAchievement->user_id = $single_user_achievement->user_id;
                    $userAchievement->title = $single_user_achievement->title;
                    $userAchievement->description = $single_user_achievement->description;
                    $userAchievement->achievement_type = $achievement_type;
                    $userAchievement->module_id = $single_user_achievement->module_id;
                    $userAchievement->module_title = $single_user_achievement->module_title;
                    $userAchievement->module_parent_id = $single_user_achievement->module_parent_id;
                    $userAchievement->module_parent_title = $single_user_achievement->module_parent_title;
                    $userAchievement->achievement_prize = $single_user_achievement->achievement_prize;
                    $userAchievement->achievement_points = $single_user_achievement->achievement_points;
                    $userAchievement->achievement_image = $single_user_achievement->achievement_image;
                    $userAchievement->issue_date = $single_user_achievement->issue_date;
                    $userAchievement->valid_date = $single_user_achievement->valid_date;
                    $userAchievement->user_notified = $single_user_achievement->user_notified;
                    $userAchievement->promo_code = $single_user_achievement->promo_code;
                    $userAchievement->save();
                }
            });

            DB::commit();
            $this->info('Migrating of old data for users table completed.');
        }catch(\Exception $e){
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
