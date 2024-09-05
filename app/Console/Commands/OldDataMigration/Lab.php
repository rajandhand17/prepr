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
    protected $signature = 'migrate-old-data:labs';
    protected $description = 'Migrate old lab table data to new db structure';

    public function handle()
    {
        try {
            $this->info('Migration of old Labs data started.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('labs')->chunkById(1000, function ($labs) {
                foreach ($labs as $lab) {
                    $checkUser = User::find($lab->user_id);
                    $checkOrganization = Organization::find($lab->organisation);
                    
                    if (!$checkUser || !$checkOrganization) {
                        continue;
                    }

                    // Set category
                    $category = $this->getCategoryId($lab->category);

                    // Handle new lab creation or fetching existing one
                    $newLab = ModelsLab::firstOrCreate(['id' => $lab->id], [
                        'uuid' => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                        'user_id' => $lab->user_id,
                        'organization_id' => $lab->organisation,
                        'type' => '4',
                        'status' => '1'
                    ]);

                    $this->mapLabAttributes($newLab, $lab, $category);

                    // Save Lab Address
                    LabAddress::updateOrCreate(
                        ['lab_id' => $lab->id],
                        [
                            'latitude' => $lab->latitute,
                            'longitude' => $lab->longitude,
                            'address' => $lab->address,
                            'city' => $lab->city,
                            'country' => $lab->country
                        ]
                    );

                    // Process modes, types, skills, tags, etc.
                    $this->processLabModes($lab);
                    $this->processLabSkills($lab);
                    $this->processLabSocialLinks($lab);
                    $this->processLabAchievements($lab);
                }
            });

            DB::commit();
            $this->info('Migration of old Labs data completed.');

        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());
        }
    }

    private function getCategoryId($oldCategoryId)
    {
        if ($oldCategoryId) {
            $checkOldCategory = DB::connection('mysql2')->table('categories')->find($oldCategoryId);
            $checkCategory = Category::where('title', $checkOldCategory->name)->first();
            return $checkCategory ? $checkCategory->id : '1';
        }
        return '1';
    }

    private function mapLabAttributes($newLab, $lab, $category)
    {
        $newLab->fill([
            'category_id' => $category,
            'slug' => $lab->slug,
            'title' => $lab->title,
            'description' => $lab->description,
            'privacy' => $this->mapPrivacy($lab->privacy),
            'media_type' => $this->mapMediaType($lab->mediaType),
            'media' => $lab->image,
            'is_auto_created' => $this->mapFlag($lab->is_auto_created),
            'is_resource_sequential' => $this->mapFlag($lab->res_sequence),
            'is_sequential' => $this->mapFlag($lab->cha_sequence),
            'is_achievement_enabled' => $this->mapFlag($lab->enable_achievement),
            'is_verified' => $this->mapFlag($lab->verification),
        ]);
        $newLab->save();
    }

    private function mapPrivacy($privacy)
    {
        return config('constants.lab_privacy.' . ($privacy ?? 'yes'));
    }

    private function mapMediaType($mediaType)
    {
        return $mediaType === 'embeddedCode' ? 'embedded' : 'image';
    }

    private function mapFlag($flag)
    {
        return $flag == '1' ? '1' : '0';
    }

    private function processLabModes($lab)
    {
        $this->processGroupTags($lab, 'mode', [
            '196' => '4',
            '197' => '5'
        ], '1');
    }

    private function processLabSkills($lab)
    {
        // Skills
        $this->processSkills($lab, 'lab_skills', '0');
        // Skill Stacks
        $this->processSkills($lab, 'skill_stacks', '2');
        // Skill Groups
        $this->processSkills($lab, 'skill_groups', '1');
    }

    private function processSkills($lab, $field, $type)
    {
        $skills = json_decode($lab->$field, true) ?: [];
        LabSkillsGroupsStack::where(['lab_id' => $lab->id, 'type' => $type])->delete();
        foreach ($skills as $skill) {
            LabSkillsGroupsStack::create(['lab_id' => $lab->id, 'foreign_id' => $skill, 'type' => $type]);
        }
    }

    private function processLabSocialLinks($lab)
    {
        $socialLinks = DB::connection('mysql2')
            ->table('lab_sociallink')
            ->where('lab_id', $lab->id)
            ->whereNull('deleted_at')
            ->get();

        if ($socialLinks->isNotEmpty()) {
            LabExternalLinks::where('lab_id', $lab->id)->delete();
            foreach ($socialLinks as $link) {
                LabExternalLinks::create([
                    'lab_id' => $link->lab_id,
                    'social_media_link' => $link->link_url,
                    'social_link_id' => $link->social_link_id
                ]);
            }
        }
    }

    private function processLabAchievements($lab)
    {
        $achievement = DB::connection('mysql2')->table('lab_achievements')->where('lab_id', $lab->id)->first();
        if ($achievement) {
            LabAcheivement::updateOrCreate(
                ['lab_id' => $achievement->lab_id],
                [
                    'achievement_name' => $achievement->achievement_name,
                    'achievement_points' => $achievement->achievement_points,
                    'achievement_condition' => json_decode($achievement->achievement_condition),
                    'achievement_image' => $achievement->achievement_image
                ]
            );
        }
    }

    private function processGroupTags($lab, $groupType, $mapping, $typeMode)
    {
        $groupTags = DB::connection('mysql2')->table('manage_tag_group')
            ->where(['module_id' => $lab->id, 'module_type' => 'lab', 'group_type' => $groupType])
            ->pluck('group_tag_id')->first();

        $tagValues = json_decode($groupTags, true) ?: [];
        LabTypeModes::where(['lab_id' => $lab->id, 'type_mode' => $typeMode])->delete();
        
        foreach ($tagValues as $tag) {
            $modeValue = $mapping[$tag] ?? null;
            if ($modeValue) {
                LabTypeModes::create(['lab_id' => $lab->id, 'type_mode' => $typeMode, 'value' => $modeValue]);
            }
        }
    }
}
