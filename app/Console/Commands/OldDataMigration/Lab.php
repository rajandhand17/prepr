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

                    $newLab->id = $lab->id;
                    $newLab->type = '4';
                    $newLab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newLab->language = $lab->language;
                    $newLab->user_id = $lab->user_id;
                    $newLab->organization_id = $lab->organisation;
                    $newLab->category_id = $category;
                    $newLab->duration_id = '1';
                    $newLab->level_id = '1';
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
