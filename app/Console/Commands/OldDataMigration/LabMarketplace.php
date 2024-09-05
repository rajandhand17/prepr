<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\LabMarketplace as ModelsLabMarketplace;
use App\Models\LabMarketplaceAddress;
use App\Models\LabMarketplaceAchievement;
use App\Models\LabMarketplaceExternalLink;
use App\Models\LabMarketplaceSkillsGroupsStack;
use App\Models\LabMarketplaceTagsGroups;
use App\Models\Organization;
use App\Models\SocialLink;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LabMarketplace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:lab-marketplace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old lab marketplace table data to new database structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migration of old data for lab marketplace started.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('template_labs')->chunkById(1000, function ($labTemplates) {
                foreach ($labTemplates as $labTemplate) {
                    // Fetch user and organization, skip if not found
                    if (!$this->userExists($labTemplate->user_id) || !$this->organizationExists($labTemplate->organisation)) {
                        continue;
                    }

                    // Get or create new marketplace entry
                    $newLabMarketplace = ModelsLabMarketplace::firstOrNew(['id' => $labTemplate->id]);

                    // Set attributes and save newLabMarketplace
                    $newLabMarketplace->fill([
                        'uuid' => $newLabMarketplace->uuid ?? Randomize::chars(10)->alphanumeric()->unique()->generate(),
                        'type' => '4',
                        'language' => $labTemplate->language,
                        'user_id' => $labTemplate->user_id,
                        'organization_id' => $labTemplate->organisation,
                        'category_id' => $this->getCategoryId($labTemplate->category),
                        'slug' => $labTemplate->slug,
                        'title' => $labTemplate->title,
                        'description' => $labTemplate->description,
                        'privacy' => $this->getPrivacySetting($labTemplate->privacy),
                        'media_type' => $this->getMediaType($labTemplate->mediaType),
                        'media' => $labTemplate->image,
                        'status' => '1',
                        'total_share' => $labTemplate->total_share,
                        'is_auto_created' => $this->parseBoolean($labTemplate->is_auto_created),
                        'is_resource_sequential' => $this->parseBoolean($labTemplate->res_sequence),
                        'is_sequential' => $this->parseBoolean($labTemplate->cha_sequence),
                        'is_achievement_enabled' => $this->parseBoolean($labTemplate->enable_achievement),
                        'is_notification_enabled' => '0',
                        'is_verified' => $this->parseBoolean($labTemplate->verification),
                    ])->save();

                    // Migrate related data
                    $this->migrateLabAddress($labTemplate);
                    $this->migrateLabSkills($labTemplate);
                    $this->migrateLabTags($labTemplate);
                    $this->migrateLabSocialLinks($labTemplate);
                    $this->migrateLabAchievements($labTemplate);
                }
            });

            DB::commit();
            $this->info('Migration of old data for lab marketplace completed.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());
        }
    }

    private function userExists($userId)
    {
        return User::find($userId);
    }

    private function organizationExists($orgId)
    {
        return Organization::find($orgId);
    }

    private function getCategoryId($oldCategoryId)
    {
        if (!$oldCategoryId) {
            return 1; // default category
        }

        $oldCategory = DB::connection('mysql2')->table('categories')->find($oldCategoryId);
        return Category::where('title', $oldCategory->name)->value('id') ?? 1;
    }

    private function getPrivacySetting($privacy)
    {
        return match ($privacy) {
            'private' => config('constants.lab_privacy.yes'),
            'public' => config('constants.lab_privacy.no'),
            default => config('constants.lab_privacy.yes'),
        };
    }

    private function getMediaType($mediaType)
    {
        return match ($mediaType) {
            'embeddedCode' => 'embedded',
            default => 'image',
        };
    }

    private function parseBoolean($value)
    {
        return $value == '1' ? '1' : '0';
    }

    private function migrateLabAddress($labTemplate)
    {
        LabMarketplaceAddress::updateOrCreate(
            ['lab_marketplace_id' => $labTemplate->id],
            [
                'latitude' => $labTemplate->latitute,
                'longitude' => $labTemplate->longitude,
                'address' => $labTemplate->address,
                'city' => $labTemplate->city,
                'country' => $labTemplate->country,
            ]
        );
    }

    private function migrateLabSkills($labTemplate)
    {
        $skills = json_decode($labTemplate->lab_skills, true);
        if ($skills) {
            LabMarketplaceSkillsGroupsStack::where('lab_marketplace_id', $labTemplate->id)->where('foreign_id', '0')->delete();
            foreach ($skills as $skill) {
                LabMarketplaceSkillsGroupsStack::create([
                    'lab_marketplace_id' => $labTemplate->id,
                    'foreign_id' => $skill,
                    'type' => '0',
                ]);
            }
        }

        $this->migrateSkillStack($labTemplate, 'skill_stacks', '2');
        $this->migrateSkillStack($labTemplate, 'skill_groups', '1');
    }

    private function migrateSkillStack($labTemplate, $attribute, $type)
    {
        $skills = explode(',', $labTemplate->$attribute);
        if ($skills) {
            LabMarketplaceSkillsGroupsStack::where('lab_marketplace_id', $labTemplate->id)->where('foreign_id', $type)->delete();
            foreach ($skills as $skill) {
                LabMarketplaceSkillsGroupsStack::create([
                    'lab_marketplace_id' => $labTemplate->id,
                    'foreign_id' => $skill,
                    'type' => $type,
                ]);
            }
        }
    }

    private function migrateLabTags($labTemplate)
    {
        $tags = json_decode($labTemplate->tags, true);
        if ($tags) {
            LabMarketplaceTagsGroups::where('lab_marketplace_id', $labTemplate->id)->where('foreign_id', '0')->delete();
            foreach ($tags as $tag) {
                LabMarketplaceTagsGroups::create([
                    'lab_marketplace_id' => $labTemplate->id,
                    'foreign_id' => $tag,
                    'type' => '0',
                ]);
            }
        }
    }

    private function migrateLabSocialLinks($labTemplate)
    {
        $socialLinks = DB::connection('mysql2')->table('template_lab_sociallink')
            ->where('lab_id', $labTemplate->id)
            ->whereNull('deleted_at')
            ->whereNotNull('social_link_id')
            ->whereNotNull('link_url')
            ->get();

        if ($socialLinks->isNotEmpty()) {
            LabMarketplaceExternalLink::where('lab_marketplace_id', $labTemplate->id)->delete();
            foreach ($socialLinks as $socialLink) {
                if (SocialLink::find($socialLink->social_link_id)) {
                    LabMarketplaceExternalLink::create([
                        'lab_marketplace_id' => $socialLink->lab_id,
                        'social_media_link' => $socialLink->link_url,
                        'social_link_id' => $socialLink->social_link_id,
                    ]);
                }
            }
        }
    }

    private function migrateLabAchievements($labTemplate)
    {
        $achievement = DB::connection('mysql2')->table('template_lab_achievements')
            ->where('lab_id', $labTemplate->id)
            ->whereNull('deleted_at')
            ->first();

        if ($achievement) {
            LabMarketplaceAchievement::updateOrCreate(
                ['lab_marketplace_id' => $achievement->lab_id],
                [
                    'achievement_name' => $achievement->achievement_name,
                    'achievement_points' => $achievement->achievement_points,
                    'achievement_condition' => json_decode($achievement->achievement_condition),
                    'achievement_image' => $achievement->achievement_image,
                ]
            );
        }
    }
}
