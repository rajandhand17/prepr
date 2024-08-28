<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\ChallengePath as ModelsChallengePath;
use App\Models\ChallengePathAchievement;
use App\Models\ChallengePathsTypeMode;
use App\Models\ComponentAssociation;
use App\Models\Organization;
use App\Models\ResourceModuleTypeModes;
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
                    $getTagGroups = DB::connection('mysql2')->table('manage_tag_group')->where(['module_id' => $challengePath->id, 'module_type' => 'challenge_path']);
                    // Clone the query to avoid modifying the original

                    $getDuration = clone $getTagGroups;
                    $duration = $getDuration->where('group_type', 'duration')->pluck('group_tag_id')->first();
                    $duration_id = 1;
                    if ($duration) {
                        if ($duration == '["169"]') {
                            $duration_id = '1';
                        } elseif ($duration == '["170"]') {
                            $duration_id = '2';
                        } elseif ($duration == '["171"]') {
                            $duration_id = '3';
                        } elseif ($duration == '["172"]') {
                            $duration_id = '4';
                        } elseif ($duration == '["173"]') {
                            $duration_id = '5';
                        } elseif ($duration == '["174"]') {
                            $duration_id = '6';
                        }
                    }
                    $getLevel = clone $getTagGroups;
                    $level = $getLevel->where('group_type', 'level')->pluck('group_tag_id')->first();
                    $level_id = 1;
                    if ($level) {
                        if ($level == '["157"]') {
                            $level_id = '1';
                        } elseif ($level == '["158"]') {
                            $level_id = '2';
                        } elseif ($level == '["159"]') {
                            $level_id = '3';
                        } elseif ($level == '["160"]') {
                            $level_id = '4';
                        }
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
                    $newChallengePath->duration_id = $duration_id;
                    $newChallengePath->level_id = $level_id;
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

                            if($sequence==null){
                                $sequence = 1;
                            }else{
                                $sequence=$sequence->sequence;
                            }
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

                    //for mode and type
                    $getMode = clone $getTagGroups;
                    $mode = $getMode->where('group_type', 'mode')->pluck('group_tag_id')->first();
                    if ($mode) {
                        $modes = json_decode($mode, true);
                        if (!empty($modes)) {
                            ChallengePathsTypeMode::where(['challenge_path_id' => $challengePath->id, 'type_mode' => '1'])->delete();
                            foreach ($modes as $single_mode) {

                                if ($single_mode == '196') {
                                    $mode_id = '4';
                                } elseif ($single_mode == '197') {
                                    $mode_id = '5';
                                }
                                $resourceMode = new ChallengePathsTypeMode();
                                $resourceMode->challenge_path_id = $challengePath->id;
                                $resourceMode->type_mode = '1';
                                $resourceMode->value = $mode_id;
                                $resourceMode->save();
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
