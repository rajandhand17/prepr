<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\ComponentAssociation;
use App\Models\LabProgram as ModelsLabProgram;
use App\Models\LabProgramsAchievement;
use App\Models\LabProgramTypeModes;
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

                    $getTagGroups = DB::connection('mysql2')->table('manage_tag_group')->where(['module_id' => $labProgram->id, 'module_type' => 'lab_program']);

                    // Clone the query to avoid modifying the original
                    $getDuration = clone $getTagGroups;
                    $duration = $getDuration->where('group_type', 'duration')->pluck('group_tag_id')->first();
                    $duration_id = null;
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
                    $level_id = null;
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
                    $newLabProgram->duration_id = $duration_id;
                    $newLabProgram->level_id = $level_id;
                    $newLabProgram->media_type = 'image';
                    $newLabProgram->media = $labProgram->group_image;
                    $newLabProgram->privacy = $labProgramPrivacy;
                    $newLabProgram->status = '1';
                    $newLabProgram->is_auto_created = $is_auto_created_labProgram;
                    $newLabProgram->is_achievement_enabled = '1';
                    $newLabProgram->is_sequential = '0';
                    $newLabProgram->is_accessible = $labProgram->is_accessable;
                    $newLabProgram->save();

                    //for mode and type
                    $getMode = clone $getTagGroups;
                    $mode = $getMode->where('group_type', 'mode')->pluck('group_tag_id')->first();
                    if ($mode) {
                        $modes = json_decode($mode, true);
                        if (!empty($modes)) {
                            LabProgramTypeModes::where(['lab_program_id' => $labProgram->id, 'type_mode' => '1'])->delete();
                            $mode_id = null;
                            foreach ($modes as $single_mode) {
                                if ($single_mode == '196') {
                                    $mode_id = '4';
                                } elseif ($single_mode == '197') {
                                    $mode_id = '5';
                                }
                                if ($mode_id != null) {
                                    $labProgramModes = new LabProgramTypeModes();
                                    $labProgramModes->lab_program_id = $labProgram->id;
                                    $labProgramModes->type_mode = '1';
                                    $labProgramModes->value = $mode_id;
                                    $labProgramModes->save();
                                }
                            }
                        }
                    }

                    $getType = clone $getTagGroups;
                    $type = $getType->where('group_type', 'type')->pluck('group_tag_id')->first();
                    if ($type) {
                        $types = json_decode($type, true);
                        if (!empty($types)) {
                            LabProgramTypeModes::where(['lab_program_id' => $labProgram->id, 'type_mode' => '0'])->delete();
                            $type_id = null;
                            foreach ($types as $single_type) {
                                if ($single_type == '192') {
                                    $type_id = '0';
                                } elseif ($single_type == '193') {
                                    $type_id = '1';
                                } elseif ($single_type == '194') {
                                    $type_id = '2';
                                } elseif ($single_type == '195') {
                                    $type_id = '3';
                                }
                                if ($type_id != null) {
                                    $labProgramType = new LabProgramTypeModes();
                                    $labProgramType->lab_program_id = $labProgram->id;
                                    $labProgramType->type_mode = '0';
                                    $labProgramType->value = $type_id;
                                    $labProgramType->save();
                                }
                            }
                        }
                    }

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
