<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\ComponentAssociation;
use App\Models\LabProgram as ModelsLabProgram;
use App\Models\LabProgramsAchievement;
use App\Models\Organization;
use App\Models\User;
use App\Services\Manage\LabService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LabProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:lab-program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old Lab Programs table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for Lab Program table started.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('groups')->where('type', 'lab')->chunkById(1000, function ($labPrograms) {
                foreach ($labPrograms as $labProgram) {
                    $checkUser = User::find($labProgram->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $checkOrganization = Organization::find($labProgram->organisation);
                    if (!$checkOrganization) {
                        continue;
                    }

                    $category = '1';
                    if ($labProgram->category != '0' && $labProgram->category != null) {
                        $checkOldCategory = DB::connection('mysql2')->table('categories')->find($labProgram->category);
                        $checkCategory = Category::where('title', $checkOldCategory->name)->first();
                        if ($checkCategory) {
                            $category = $checkCategory->id;
                        }
                    }

                    $checkLabProgram = ModelsLabProgram::where('id', $labProgram->id)->first();
                    if ($checkLabProgram) {
                        $newLabProgram = $checkLabProgram;
                    } else {
                        $newLabProgram = new ModelsLabProgram();
                    }

                    switch ($labProgram->privacy) {
                        case 'public':
                            $labProgramPrivacy = '0';
                            break;
                        case 'private':
                            $labProgramPrivacy = '1';
                            break;
                        default:
                            $labProgramPrivacy = '1';
                            break;
                    }

                    switch ($labProgram->is_auto_created) {
                        case '0':
                            $is_auto_created_labProgram = '0';
                            break;
                        case '1':
                            $is_auto_created_labProgram = '1';
                            break;
                        default:
                            $is_auto_created_labProgram = '0';
                            break;
                    }

                    $labProgramModel = new ModelsLabProgram();

                    $newLabProgram->id = $labProgram->id;
                    $newLabProgram->language = $labProgram->language;
                    $newLabProgram->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newLabProgram->title = $labProgram->title;
                    $newLabProgram->slug = UtilityHelper::generateSlug($labProgram->title, $labProgramModel);
                    $newLabProgram->description = $labProgram->description;
                    $newLabProgram->user_id = $labProgram->user_id;
                    $newLabProgram->organization_id = $labProgram->organisation;
                    $newLabProgram->category_id = $category;
                    $newLabProgram->duration_id = '1';
                    $newLabProgram->level_id = '1';
                    $newLabProgram->media_type = 'image';
                    $newLabProgram->media = $labProgram->group_image;
                    $newLabProgram->privacy = $labProgramPrivacy;
                    $newLabProgram->status = '1';
                    $newLabProgram->is_auto_created = $is_auto_created_labProgram;
                    $newLabProgram->is_achievement_enabled = '1';
                    $newLabProgram->is_sequential = '0';
                    $newLabProgram->is_accessible = $labProgram->is_accessable;
                    $newLabProgram->save();

                    // For Lab Program Achievement
                    $checkLabProgramAchievement = LabProgramsAchievement::where('lab_program_id', $labProgram->id)->first();
                    if ($checkLabProgramAchievement) {
                        $newLabProgramAchievement = $checkLabProgramAchievement;
                    } else {
                        $newLabProgramAchievement = new LabProgramsAchievement();
                    }
                    $newLabProgramAchievement->lab_program_id = $labProgram->id;
                    $newLabProgramAchievement->achievement_name = $labProgram->prize;
                    $newLabProgramAchievement->achievement_points = $labProgram->points;
                    $newLabProgramAchievement->achievement_image = $labProgram->trophy;
                    $newLabProgramAchievement->save();

                    // For Lab Program Association
                    if (!empty($labProgram->lab_id)) {
                        $labIdArray = explode(',', $labProgram->lab_id);
                        $sequence = 1;
                        $getLabId = LabService::getLabIdBasedOnId($labIdArray);
                        if (!empty($getLabId)) {
                            $existComponentAssociation = ComponentAssociation::where([
                                ['lab_program_id', '=', $labProgram->id],
                                ['lab_id', '!=', null],
                            ])->pluck('lab_id')->all();
                            $newComponentAssociation = array_diff($getLabId, $existComponentAssociation);
                            ComponentAssociation::where('lab_program_id', $labProgram->id)->whereIn('lab_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['lab_program_id', '=', $labProgram->id],
                                ['lab_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            foreach ($newComponentAssociation as $lab_id) {
                                $sequence++;
                                $labProgramAssociation = new ComponentAssociation();
                                $labProgramAssociation->lab_program_id = $labProgram->id;
                                $labProgramAssociation->lab_id = $lab_id;
                                $labProgramAssociation->sequence = $sequence;
                                $labProgramAssociation->save();
                            }
                        }
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for Lab Programs table completed.');

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
