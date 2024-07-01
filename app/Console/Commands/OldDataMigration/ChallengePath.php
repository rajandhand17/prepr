<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\ChallengePath as ModelsChallengePath;
use App\Models\ChallengePathAchievement;
use App\Models\ComponentAssociation;
use App\Models\Organization;
use App\Models\User;
use App\Services\Manage\ChallengeService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChallengePath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:challenge-path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old Challenge Paths table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for Challenge Path table started.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('groups')->where('type', 'challenge')->chunkById(1000, function ($challengePaths) {
                foreach ($challengePaths as $challengePath) {
                    $checkUser = User::find($challengePath->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $checkOrganization = Organization::find($challengePath->organisation);
                    if (!$checkOrganization) {
                        continue;
                    }

                    $category = '1';
                    if ($challengePath->category != '0' && $challengePath->category != null) {
                        $checkOldCategory = DB::connection('mysql2')->table('categories')->find($challengePath->category);
                        $checkCategory = Category::where('title', $checkOldCategory->name)->first();
                        if ($checkCategory) {
                            $category = $checkCategory->id;
                        }
                    }

                    $checkChallengePath = ModelsChallengePath::where('id', $challengePath->id)->first();
                    if ($checkChallengePath) {
                        $newChallengePath = $checkChallengePath;
                    } else {
                        $newChallengePath = new ModelsChallengePath();
                    }

                    switch ($challengePath->privacy) {
                        case 'public':
                            $challengePathPrivacy = '0';
                            break;
                        case 'private':
                            $challengePathPrivacy = '1';
                            break;
                        default:
                            $challengePathPrivacy = '1';
                            break;
                    }

                    switch ($challengePath->is_auto_created) {
                        case '0':
                            $is_auto_created_challengePath = '0';
                            break;
                        case '1':
                            $is_auto_created_challengePath = '1';
                            break;
                        default:
                            $is_auto_created_challengePath = '0';
                            break;
                    }

                    switch ($challengePath->published) {
                        case 'published':
                            $challengePathPublished = '1';
                            break;
                        case 'draft':
                            $challengePathPublished = '0';
                            break;
                        default:
                            $challengePathPublished = '1';
                            break;
                    }

                    $challengePathModel = new ModelsChallengePath();

                    $newChallengePath->id = $challengePath->id;
                    $newChallengePath->language = $challengePath->language;
                    $newChallengePath->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newChallengePath->title = $challengePath->title;
                    $newChallengePath->slug = UtilityHelper::generateSlug($challengePath->title, $challengePathModel);
                    $newChallengePath->description = $challengePath->description;
                    $newChallengePath->user_id = $challengePath->user_id;
                    $newChallengePath->organization_id = $challengePath->organisation;
                    $newChallengePath->category_id = $category;
                    $newChallengePath->duration_id = '1';
                    $newChallengePath->level_id = '1';
                    $newChallengePath->media_type = 'image';
                    $newChallengePath->media = $challengePath->group_image;
                    $newChallengePath->privacy = $challengePathPrivacy;
                    $newChallengePath->status = $challengePathPublished;
                    $newChallengePath->is_auto_created = $is_auto_created_challengePath;
                    $newChallengePath->is_achievement_enabled = '1';
                    $newChallengePath->is_sequential = '0';
                    $newChallengePath->is_accessible = $challengePath->is_accessable;
                    $newChallengePath->save();

                    // For Challenge Path Achievement
                    $checkChallengePathAchievement = ChallengePathAchievement::where('challenge_path_id', $challengePath->id)->first();
                    if ($checkChallengePathAchievement) {
                        $newChallengePathAchievement = $checkChallengePathAchievement;
                    } else {
                        $newChallengePathAchievement = new ChallengePathAchievement();
                    }
                    $newChallengePathAchievement->challenge_path_id = $challengePath->id;
                    $newChallengePathAchievement->achievement_name = $challengePath->prize ?? null;
                    $newChallengePathAchievement->achievement_points = $challengePath->points ?? null;
                    $newChallengePathAchievement->achievement_image = $challengePath->trophy ?? null;
                    $newChallengePathAchievement->save();

                    // For Challenge Path Association
                    if (!empty($challengePath->challenge_id)) {
                        $challengeIdArray = explode(',', $challengePath->challenge_id);
                        $sequence = 1;
                        $getChallengeId = ChallengeService::getChallengeIdBasedOnId($challengeIdArray);
                        if (!empty($getChallengeId)) {
                            $existComponentAssociation = ComponentAssociation::where([
                                ['challenge_path_id', '=', $challengePath->id],
                                ['challenge_id', '!=', null],
                            ])->pluck('challenge_id')->all();
                            $newComponentAssociation = array_diff($getChallengeId, $existComponentAssociation);
                            ComponentAssociation::where('challenge_path_id', $challengePath->id)->whereIn('challenge_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['challenge_path_id', '=', $challengePath->id],
                                ['challenge_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            $newRecordsComponentAssociation = array_diff($existComponentAssociation, $getChallengeId);
                            foreach ($newRecordsComponentAssociation as $challenge_id) {
                                $sequence++;
                                $challengePathAssociation = new ComponentAssociation();
                                $challengePathAssociation->challenge_path_id = $challengePath->id;
                                $challengePathAssociation->challenge_id = $challenge_id;
                                $challengePathAssociation->sequence = $sequence;
                                $challengePathAssociation->save();
                            }
                        }
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for Challenge Paths table completed.');

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
