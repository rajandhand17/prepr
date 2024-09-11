<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Lab as ModelsLab;
use App\Models\LabAcheivement;
use App\Models\LabAddress;
use App\Models\LabExternalLinks;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTagsGroups;
use App\Models\LabTypeModes;
use App\Models\Organization;
use App\Models\SocialLink;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Lab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:labs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old lab table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for Labs table started.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('labs')->chunkById(1000, function ($labs) {
                foreach ($labs as $lab) {
                    $checkUser = User::find($lab->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $checkOrganizatioon = Organization::find($lab->organisation);
                    if (!$checkOrganizatioon) {
                        continue;
                    }

                    $category = '1';
                    if ($lab->category) {
                        $checkOldCategory = DB::connection('mysql2')->table('categories')->find($lab->category);
                        $checkCategory = Category::where('title', $checkOldCategory->name)->first();
                        if ($checkCategory) {
                            $category = $checkCategory->id;
                        }
                    }

                    $checkLab = ModelsLab::find($lab->id);
                    if ($checkLab) {
                        $newLab = $checkLab;
                    } else {
                        $newLab = new ModelsLab();
                    }

                    $privacy = config('constants.lab_privacy.no');
                    switch ($lab->privacy) {
                        case 'yes':
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                        case 'no':
                            $privacy = config('constants.lab_privacy.no');
                            break;
                        default:
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                    }

                    switch ($lab->res_sequence) {
                        case '0':
                            $res_sequencial = '0';
                            break;
                        case '1':
                            $res_sequencial = '1';
                            break;
                        default:
                            $res_sequencial = '0';
                            break;
                    }

                    switch ($lab->cha_sequence) {
                        case '0':
                            $cha_sequencial = '0';
                            break;
                        case '1':
                            $cha_sequencial = '1';
                            break;
                        default:
                            $cha_sequencial = '0';
                            break;
                    }

                    switch ($lab->enable_achievement) {
                        case '0':
                            $enable_achievement = '0';
                            break;
                        case '1':
                            $enable_achievement = '1';
                            break;
                        default:
                            $enable_achievement = '0';
                            break;
                    }

                    switch ($lab->verification) {
                        case '0':
                            $lab_verfied = '0';
                            break;
                        case '1':
                            $lab_verfied = '1';
                            break;
                        default:
                            $lab_verfied = '0';
                            break;
                    }

                    switch ($lab->is_auto_created) {
                        case '0':
                            $is_auto_created_lab = '0';
                            break;
                        case '1':
                            $is_auto_created_lab = '1';
                            break;
                        default:
                            $is_auto_created_lab = '0';
                            break;
                    }

                    $mediaType = 'image';
                    switch ($lab->mediaType) {
                        case 'image':
                            $mediaType = 'image';
                            break;
                        case 'embeddedCode':
                            $mediaType = 'embedded';
                            break;
                        default:
                            $mediaType = 'image';
                            break;
                    }

                    $getTagGroups = DB::connection('mysql2')->table('manage_tag_group')->where(['module_id' => $lab->id, 'module_type' => 'lab']);
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

                    $createdAt = $lab->created_at != null ? Carbon::createFromTimestamp($lab->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                    $updatedAt = $lab->updated_at != null ? Carbon::createFromTimestamp($lab->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                    $deletedAt = $lab->deleted_at != null ? Carbon::createFromTimestamp($lab->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;


                    $newLab->id = $lab->id;
                    $newLab->type = '4';
                    $newLab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newLab->language = $lab->language;
                    $newLab->user_id = $lab->user_id;
                    $newLab->organization_id = $lab->organisation;
                    $newLab->category_id = $category;
                    $newLab->duration_id = $duration_id;
                    $newLab->level_id = $level_id;
                    $newLab->slug = $lab->slug;
                    $newLab->title = $lab->title;
                    $newLab->description = $lab->description;
                    $newLab->privacy = $privacy;
                    $newLab->media_type = $mediaType;
                    $newLab->media = $lab->image;
                    $newLab->status = '1';
                    $newLab->total_share = $lab->total_share;
                    $newLab->is_auto_created = $is_auto_created_lab;
                    $newLab->is_resource_sequential = $res_sequencial;
                    $newLab->is_sequential = $cha_sequencial;
                    $newLab->is_achievement_enabled = $enable_achievement;
                    $newLab->is_notification_enabled = '0';
                    $newLab->is_verified = $lab_verfied;
                    $newLab->created_at = $createdAt;
                    $newLab->updated_at = $updatedAt;
                    $newLab->deleted_at = $deletedAt;
                    $newLab->save();

                    // For Lab Address
                    $checkLabAddress = LabAddress::where('lab_id', $lab->id)->first();
                    if ($checkLabAddress) {
                        $newLabAddress = $checkLabAddress;
                    } else {
                        $newLabAddress = new LabAddress();
                    }

                    $newLabAddress->lab_id = $lab->id;
                    $newLabAddress->latitude = $lab->latitute;
                    $newLabAddress->longitude = $lab->longitude;
                    $newLabAddress->address = $lab->address;
                    $newLabAddress->city = $lab->city;
                    $newLabAddress->country = $lab->country;
                    $newLabAddress->save();

                    //for mode and type
                    $getMode = clone $getTagGroups;
                    $mode = $getMode->where('group_type', 'mode')->pluck('group_tag_id')->first();
                    if ($mode) {
                        $modes = json_decode($mode, true);
                        if (!empty($modes)) {
                            LabTypeModes::where(['lab_id' => $lab->id, 'type_mode' => '1'])->delete();
                            $mode_id = null;
                            foreach ($modes as $single_mode) {
                                if ($single_mode == '196') {
                                    $mode_id = '4';
                                } elseif ($single_mode == '197') {
                                    $mode_id = '5';
                                }
                                if ($mode_id != null) {
                                    $labMode = new LabTypeModes();
                                    $labMode->lab_id = $lab->id;
                                    $labMode->type_mode = '1';
                                    $labMode->value = $mode_id;
                                    $labMode->save();
                                }
                            }
                        }
                    }

                    $getType = clone $getTagGroups;
                    $type = $getType->where('group_type', 'type')->pluck('group_tag_id')->first();
                    if ($type) {
                        $types = json_decode($type, true);
                        if (!empty($types)) {
                            LabTypeModes::where(['lab_id' => $lab->id, 'type_mode' => '0'])->delete();
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
                                    $labMode = new LabTypeModes();
                                    $labMode->lab_id = $lab->id;
                                    $labMode->type_mode = '0';
                                    $labMode->value = $type_id;
                                    $labMode->save();
                                }
                            }
                        }
                    }

                    // For Lab Skill
                    $arraySkills = json_decode($lab->lab_skills, true);
                    if (!empty($arraySkills)) {
                        LabSkillsGroupsStack::where(['lab_id' => $lab->id, 'foreign_id' => '0'])->delete();
                        foreach (array_filter($arraySkills) as $skill) {
                            $labSkill = new LabSkillsGroupsStack();
                            $labSkill->lab_id = $lab->id;
                            $labSkill->foreign_id = $skill;
                            $labSkill->type = '0';
                            $labSkill->save();
                        }
                    }

                    // For Lab Skill Stack
                    $skillStacks = $lab->skill_stacks;
                    if (!empty($skillStacks)) {
                        LabSkillsGroupsStack::where(['lab_id' => $lab->id, 'foreign_id' => '2'])->delete();
                        foreach (explode(',', $skillStacks) as $skillStack) {
                            $labSkillStack = new LabSkillsGroupsStack();
                            $labSkillStack->lab_id = $lab->id;
                            $labSkillStack->foreign_id = $skillStack;
                            $labSkillStack->type = '2';
                            $labSkillStack->save();
                        }
                    }

                    // For Lab Skill Group
                    $skillGroups = $lab->skill_groups;
                    if (!empty($skillGroups)) {
                        LabSkillsGroupsStack::where(['lab_id' => $lab->id, 'foreign_id' => '1'])->delete();
                        foreach (explode(',', $skillGroups) as $skillGroup) {
                            $labSkillGroup = new LabSkillsGroupsStack();
                            $labSkillGroup->lab_id = $lab->id;
                            $labSkillGroup->foreign_id = $skillGroup;
                            $labSkillGroup->type = '1';
                            $labSkillGroup->save();
                        }
                    }

                    // For Lab Tag
                    $arrayTags = json_decode($lab->tags, true);
                    if (!empty($arrayTags)) {
                        LabTagsGroups::where(['lab_id' => $lab->id, 'foreign_id' => '0'])->delete();
                        foreach (array_filter($arrayTags) as $tag) {
                            $labTag = new LabTagsGroups();
                            $labTag->lab_id = $lab->id;
                            $labTag->foreign_id = $tag;
                            $labTag->type = '0';
                            $labTag->save();
                        }
                    }

                    // For Lab Social Links
                    $labSocialLinks = DB::connection('mysql2')->table('lab_sociallink')->where('lab_id', $lab->id)->whereNull('deleted_at')->whereNotNull('social_link_id')->whereNotNull('link_url')->get();
                    if ($labSocialLinks->isNotEmpty()) {
                        LabExternalLinks::where('lab_id', $lab->id)->delete();
                        foreach ($labSocialLinks as $labSocialLink) {
                            $checkSocialIdExists = SocialLink::where('id', $labSocialLink->social_link_id)->first();
                            if ($checkSocialIdExists) {
                                $newLabSocialLink = new LabExternalLinks();
                                $newLabSocialLink->lab_id = $labSocialLink->lab_id;
                                $newLabSocialLink->social_media_link = $labSocialLink->link_url;
                                $newLabSocialLink->social_link_id = $labSocialLink->social_link_id;
                                $newLabSocialLink->save();
                            }
                        }
                    }

                    // For Lab Achievements
                    $labAchevements = DB::connection('mysql2')->table('lab_achievements')->where('lab_id', $lab->id)->whereNull('deleted_at')->first();
                    if ($labAchevements) {
                        $checkLabAchievement = LabAcheivement::where('lab_id', $lab->id)->first();
                        if ($checkLabAchievement) {
                            $newLabAchievement = $checkLabAchievement;
                        } else {
                            $newLabAchievement = new LabAcheivement();
                        }

                        $newLabAchievement->lab_id = $labAchevements->lab_id;
                        $newLabAchievement->achievement_name = $labAchevements->achievement_name;
                        $newLabAchievement->achievement_points = $labAchevements->achievement_points;
                        $newLabAchievement->achievement_condition = json_decode($labAchevements->achievement_condition);
                        $newLabAchievement->achievement_image = $labAchevements->achievement_image;
                        $newLabAchievement->save();
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for Labs table completed.');

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
