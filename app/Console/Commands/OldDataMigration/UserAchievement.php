<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserAchievement as ModelUserAchievement;

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
    protected $description = 'Migrate all users achievements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting migration of user achievements.');

            DB::beginTransaction();

            DB::connection('mysql2')->table('user_achievements')->chunkById(1000, function ($userAchievements) {
                $certificateDate = Carbon::now()->format('ymd');

                foreach ($userAchievements as $key => $achievement) {
                    $user = User::find($achievement->user_id);
                    if (!$user) {
                        continue;
                    }

                    $userAchievement = ModelUserAchievement::firstOrNew(['id' => $achievement->id]);
                    $certificateNumber = $this->generateCertificateNumber($certificateDate, $key);

                    $userAchievement->fill([
                        'user_id' => $achievement->user_id,
                        'certificate_number' => $certificateNumber,
                        'title' => $achievement->title,
                        'description' => $achievement->description,
                        'achievement_type' => $this->mapAchievementType($achievement->achievement_type),
                        'module_id' => $achievement->module_id,
                        'module_title' => $achievement->module_title,
                        'module_parent_id' => $achievement->module_parent_id,
                        'module_parent_title' => $achievement->module_parent_title,
                        'achievement_prize' => $achievement->achievement_prize,
                        'achievement_points' => $achievement->achievement_points,
                        'achievement_image' => $achievement->achievement_image,
                        'issue_date' => $achievement->issue_date,
                        'valid_date' => $achievement->valid_date,
                        'user_notified' => $achievement->user_notified,
                        'promo_code' => $achievement->promo_code,
                        'created_at' => optional($achievement->created_at)->toDateTimeString(),
                        'updated_at' => optional($achievement->updated_at)->toDateTimeString(),
                        'deleted_at' => optional($achievement->deleted_at)->toDateTimeString(),
                    ]);

                    $userAchievement->save();
                }
            });

            DB::commit();
            $this->info('Migration of user achievements completed.');

        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error during migration: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique certificate number.
     *
     * @param string $certificateDate
     * @param int $key
     * @return string
     */
    private function generateCertificateNumber(string $certificateDate, int $key): string
    {
        $oldDataId = $key - 1;
        return $certificateDate . $oldDataId . '00' . $key;
    }

    /**
     * Map achievement type to the corresponding config value.
     *
     * @param string|null $type
     * @return string|null
     */
    private function mapAchievementType(?string $type): ?string
    {
        $mapping = [
            'lab' => config('constants.user_achievement_type.lab'),
            'labprogram' => config('constants.user_achievement_type.lab_program'),
            'challenge' => config('constants.user_achievement_type.challenge'),
            'challengepath' => config('constants.user_achievement_type.challenge_path'),
            'resourcegroup' => config('constants.user_achievement_type.resource_group'),
            'appreciationaward' => config('constants.user_achievement_type.appreciation_award'),
            'activityaward' => config('constants.user_achievement_type.activity_award'),
            'skillactivity' => config('constants.user_achievement_type.skill_activity'),
            'importedaward' => config('constants.user_achievement_type.imported_award'),
            'winneraward' => config('constants.user_achievement_type.winner_award'),
            'participationaward' => config('constants.user_achievement_type.participation_award'),
        ];

        return $mapping[$type] ?? null;
    }
}
