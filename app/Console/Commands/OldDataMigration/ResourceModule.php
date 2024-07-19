<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Models\ResourceModule as ResourceModules;
use App\Models\ResourceModuleDetail;
use App\Models\ResourceModuleRating as ResourceModuleRatings;
use App\Models\ResourceModuleSkillsGroupsStack;
use App\Models\ResourceModuleTagsGroups;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResourceModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old resource module table data to new db structure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for resource module table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('resources')->chunkById(1000, function ($resources) {
                foreach ($resources as  $single_resource) {
                    $checkUser = User::find($single_resource->user_id);
                    if (!$checkUser) {
                        continue;
                    }
                    $organization = Organization::find($single_resource->org_id);
                    if (!$organization) {
                        continue;
                    }

                    $resourceDetails = DB::connection('mysql2')->table('resource_module_details')->where(function ($query) {
                        $query->where('type', 'header')
                        ->orWhere('type', 'Embedded_Cover_Video');
                    })
                    ->where('resource_id', $single_resource->id)->first();

                    $mediaType = 'image';
                    $media = config('site-settings.default_resource_module_cover_image');
                    if ($resourceDetails) {
                        switch ($resourceDetails->type) {
                            case 'header':
                                $mediaType = 'image';
                                $media = $resourceDetails->path;
                                break;
                            case 'Embedded_Cover_Video':
                                $mediaType = 'embedded';
                                $media = $resourceDetails->path;
                                break;
                            default:
                                $mediaType = 'image';
                                $media = config('site-settings.default_resource_module_cover_image');
                                break;
                        }
                    }

                    switch ($single_resource->status) {
                        case 'open':
                            $privacy = config('constants.resource_module_privacy.no');
                            break;
                        case 'closed':
                            $privacy = config('constants.resource_module_privacy.yes');
                            break;
                        default:
                            $privacy = config('constants.resource_module_privacy.yes');
                            break;
                    }

                    switch ($single_resource->resourceGlobal) {
                        case '0':
                            $is_global = '0';
                            break;
                        case '1':
                            $is_global = '1';
                            break;
                        default:
                            $is_global = '0';
                            break;
                    }

                    switch ($single_resource->is_auto_created) {
                        case '0':
                            $is_auto_created_module = '0';
                            break;
                        case '1':
                            $is_auto_created_module = '1';
                            break;
                        default:
                            $is_auto_created_module = '0';
                            break;
                    }

                    $check_resource_module = ResourceModules::where('id', $single_resource->id)->first();
                    $status = config('constants.resource_module_status.publish');
                    if ($check_resource_module) {
                        $newResourceModule = $check_resource_module;
                    } else {
                        $newResourceModule = new ResourceModules();
                    }
                    $newResourceModule->id = $single_resource->id;
                    $newResourceModule->language = $single_resource->language;
                    $newResourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newResourceModule->user_id = $single_resource->user_id;
                    $newResourceModule->organization_id = $single_resource->org_id;
                    $newResourceModule->duration_id = '1';
                    $newResourceModule->level_id = '1';
                    $newResourceModule->title = $single_resource->res_title;
                    $newResourceModule->slug = $single_resource->res_title_slug;
                    $newResourceModule->description = $single_resource->res_desc;
                    $newResourceModule->media_type = $mediaType;
                    $newResourceModule->media = $media;
                    $newResourceModule->privacy = $privacy;
                    $newResourceModule->status = $status;
                    $newResourceModule->is_global = $is_global;
                    $newResourceModule->is_auto_created = $is_auto_created_module;
                    $newResourceModule->is_accessible = $single_resource->is_accessable;
                    $newResourceModule->save();

                    // For Resource Module Skills
                    $resourceSkillIdArray = json_decode($single_resource->resource_skills, true);
                    if (!empty($resourceSkillIdArray)) {
                        ResourceModuleSkillsGroupsStack::where(['id' => $single_resource->id, 'type' => '0'])->delete();
                        foreach ($resourceSkillIdArray as $resourceSkillId) {
                            $checkSkill = Skill::find($resourceSkillId);
                            if ($checkSkill) {
                                $newModuleSkill = new ResourceModuleSkillsGroupsStack();
                                $newModuleSkill->resource_module_id = $single_resource->id;
                                $newModuleSkill->foreign_id = $resourceSkillId;
                                $newModuleSkill->type = '0';
                                $newModuleSkill->save();
                            }
                        }
                    }

                    // For Resource Module Skill Stacks
                    $resourceSkillStacks = $single_resource->skill_stacks;
                    if (!empty($resourceSkillStacks)) {
                        ResourceModuleSkillsGroupsStack::where(['id' => $single_resource->id, 'type' => '1'])->delete();
                        foreach (explode(',', $resourceSkillStacks) as $resourceSkillStackId) {
                            $newModuleSkillStack = new ResourceModuleSkillsGroupsStack();
                            $newModuleSkillStack->resource_module_id = $single_resource->id;
                            $newModuleSkillStack->foreign_id = $resourceSkillStackId;
                            $newModuleSkillStack->type = '1';
                            $newModuleSkillStack->save();
                        }
                    }

                    // For Resource Module Skill Groups
                    $resourceSkillGroups = $single_resource->skill_groups;
                    if (!empty($resourceSkillGroups)) {
                        ResourceModuleSkillsGroupsStack::where(['id' => $single_resource->id, 'type' => '2'])->delete();
                        foreach (explode(',', $resourceSkillGroups) as $resourceSkillGroupId) {
                            $newModuleSkillGroup = new ResourceModuleSkillsGroupsStack();
                            $newModuleSkillGroup->resource_module_id = $single_resource->id;
                            $newModuleSkillGroup->foreign_id = $resourceSkillGroupId;
                            $newModuleSkillGroup->type = '2';
                            $newModuleSkillGroup->save();
                        }
                    }

                    // For Resource Module Tags
                    $resourceTagIdArray = json_decode($single_resource->resource_tags, true);
                    if (!empty($resourceTagIdArray)) {
                        ResourceModuleTagsGroups::where(['id' => $single_resource->id, 'type' => '0'])->delete();
                        foreach ($resourceTagIdArray as $resourceTagId) {
                            $checkTag = Tag::find($resourceTagId);
                            if ($checkTag) {
                                $newModuleTag = new ResourceModuleSkillsGroupsStack();
                                $newModuleTag->resource_module_id = $single_resource->id;
                                $newModuleTag->foreign_id = $resourceTagId;
                                $newModuleTag->type = '0';
                                $newModuleTag->save();
                            }
                        }
                    }

                    // For Resource Module Attachments
                    $resourceModuleDetails = DB::connection('mysql2')->table('resource_module_details')->whereNotIn('type', ['header', 'Embedded_Cover_Video'])->where('resource_id', $single_resource->id)->get();
                    if ($resourceModuleDetails->isNotEmpty()) {
                        foreach ($resourceModuleDetails as $moduleData) {
                            switch ($moduleData->type) {
                                case 'document':
                                    $type = config('constants.resource_module_type.document');
                                    break;
                                case 'video':
                                    $type = config('constants.resource_module_type.video');
                                    break;
                                case 'audio':
                                    $type = config('constants.resource_module_type.audio');
                                    break;
                                case 'embedded':
                                    $type = config('constants.resource_module_type.embedded_video');
                                    break;
                                case 'embedded_audio':
                                    $type = config('constants.resource_module_type.embedded_audio');
                                    break;
                                case 'url':
                                    $type = config('constants.resource_module_type.url');
                                    break;
                                case 'image':
                                    $type = config('constants.resource_module_type.image');
                                    break;
                                default:
                                    $type = null;
                                    break;
                            }

                            $linkId = $moduleData->social_link_id;
                            if ($type === 'url') {
                                $checkSocialLink = SocialLink::find($moduleData->social_link_id);
                                if (!$checkSocialLink) {
                                    $linkId = '15';
                                }
                            }
                            $newModuleAttachment = new ResourceModuleDetail();
                            $newModuleAttachment->id = $moduleData->id;
                            $newModuleAttachment->resource_module_id = $single_resource->id;
                            $newModuleAttachment->title = $moduleData->title;
                            $newModuleAttachment->type = $type;
                            $newModuleAttachment->path = $moduleData->path;
                            $newModuleAttachment->social_link_id = $linkId;
                            $newModuleAttachment->save();
                        }
                    }

                    // For Resource Module Rating
                    $resourceRatings = DB::connection('mysql2')->table('user_resource_ratings')->where('res_id', $single_resource->id)->get();
                    if ($resourceRatings->isNotEmpty()) {
                        foreach ($resourceRatings as $resourceRating) {
                            $checkRating = ResourceModuleRatings::where('resource_module_id', $single_resource->id)->first();
                            if ($checkRating) {
                                $newResourceModuleRating = $checkRating;
                            } else {
                                $newResourceModuleRating = new ResourceModuleRatings();
                            }
                            $newResourceModuleRating->resource_module_id = $single_resource->id;
                            $newResourceModuleRating->user_id = $resourceRating->user_id;
                            $newResourceModuleRating->rating = $resourceRating->ratting;
                            $newResourceModuleRating->save();
                        }
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for resource module table completed.');

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e);

            return;
        }
    }
}
