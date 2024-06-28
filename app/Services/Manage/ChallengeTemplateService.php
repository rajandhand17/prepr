<?php

namespace App\Services\Manage;

use App\Events\ChallengeTemplate\DeleteChallengeTemplateAssociatedData;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengeTemplate;
use App\Models\ComponentAssociation;
use App\Models\LabChallengeRedeem;
use App\Models\LabMarketplaceComponentAssociations;
use App\Models\Organization;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ChallengeTemplateService
{
    public static function getChallengeTemplateList($request)
    {
        try {
            $challenge_template_list = ChallengeTemplate::select()->where('language', $request->language);

            $challenge_template_list = self::filterChallengeTemplateList($challenge_template_list, $request);

            return $challenge_template_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            return false;
        }
    }

    public static function filterChallengeTemplateList($challenge_template_list, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $challenge_template_list = $challenge_template_list->where('challenge_templates.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $challenge_template_list = $challenge_template_list->orderBy('challenge_templates.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $challenge_template_list = $challenge_template_list->orderBy('challenge_templates.title', 'DESC');
                        break;
                    case 'creation_date':
                        $challenge_template_list = $challenge_template_list->orderBy('challenge_templates.created_at', 'ASC');
                        break;
                    default:
                        $challenge_template_list = $challenge_template_list->orderBy('challenge_templates.id', 'ASC');
                }
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $challenge_template_list = $challenge_template_list->whereIn('challenge_templates.category_id', $request->category);
            }

            if ($request->has('status') && !empty($request->status)) {
                $getChallengeRedeemedIds = LabChallengeRedeem::where(['organization_id' => $request->organization_id, 'is_redeemed' => '1'])->whereNotNull('challenge_id')->pluck('challenge_template_id');
                switch ($request->status) {
                    case 'redeemed':
                        $challenge_template_list = $challenge_template_list->whereIn('id', $getChallengeRedeemedIds);
                        break;
                    case 'not_redeemed':
                        $challenge_template_list = $challenge_template_list->whereNotIn('id', $getChallengeRedeemedIds);
                        break;
                    default:
                        $challenge_template_list = $challenge_template_list;
                        break;
                }
            }

            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $challenge_template_list = $challenge_template_list->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $challenge_template_list = $challenge_template_list->whereIn('level_id', $request->level_id);
            }

            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $challenge_template_list = $challenge_template_list->whereIn('challenge_templates.id', function ($query) use ($request) {
                    $query->select('challenge_template_skills_groups_stacks.challenge_template_id')
                    ->from('challenge_template_skills_groups_stacks')
                    ->whereIn('challenge_template_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('challenge_template_skills_groups_stacks.type', '0')
                        ->whereNull('challenge_template_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('challenge_templates.uuid');
            }

            return $challenge_template_list;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addChallengeTemplate($challengeId)
    {
        try {
            $originalChallenge = Challenge::find($challengeId);
            $templateChallenge = new ChallengeTemplate();
            $templateChallenge->language = $originalChallenge->language;
            $templateChallenge->uuid = $originalChallenge->uuid;
            $templateChallenge->title = $originalChallenge->title;
            $templateChallenge->slug = $originalChallenge->slug;
            $templateChallenge->user_id = auth()->user()->id;
            $templateChallenge->organization_id = $originalChallenge->organization_id;
            $templateChallenge->category_id = $originalChallenge->category_id;
            $templateChallenge->duration_id = $originalChallenge->duration_id;
            $templateChallenge->level_id = $originalChallenge->level_id;
            $templateChallenge->description = $originalChallenge->description;
            $templateChallenge->privacy = $originalChallenge->privacy;
            $templateChallenge->media_type = $originalChallenge->media_type;
            $templateChallenge->media = $originalChallenge->getRawOriginal('media');
            $templateChallenge->status = $originalChallenge->status;
            $templateChallenge->source_link = $originalChallenge->source_link;
            $templateChallenge->agreement = $originalChallenge->agreement;
            $templateChallenge->is_notification_enabled = $originalChallenge->is_notification_enabled;
            $templateChallenge->project_privacy = $originalChallenge->project_privacy;
            $templateChallenge->is_open = $originalChallenge->is_open;
            $templateChallenge->is_auto_created = $originalChallenge->is_auto_created;
            $templateChallenge->save();

            return $templateChallenge;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getChallengeTemplateBasedOnSlug($slug)
    {
        try {
            return ChallengeTemplate::where('slug', $slug)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkChallengeRedeemedOrNot($challengeTemplateId, $organizationId)
    {
        try {
            $checkChallengeRedeemed = LabChallengeRedeem::where(['challenge_template_id' => $challengeTemplateId, 'organization_id' => $organizationId, 'is_redeemed' => '1'])->exists();
            if (!$checkChallengeRedeemed) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function redeemChallengeTemplateToChallenge($challengeTemplateId, $organizationId)
    {
        try {
            $challengeTemplateData = ChallengeTemplate::find($challengeTemplateId);
            $organisationName = Organization::where('id', $organizationId)->pluck('title')->first();

            $model = new Challenge();
            $slug = UtilityHelper::generateSlug($organisationName.'-'.$challengeTemplateData->slug, $model);

            $title = $title_format = $organisationName.' '.$challengeTemplateData->title;
            $next = 1;
            while (Challenge::where('title', '=', $title)->first()) {
                $title = "{$title_format} {$next}";
                $next++;
            }

            $newChallenge = new Challenge();
            $newChallenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $newChallenge->language = $challengeTemplateData->language;
            $newChallenge->user_id = auth()->user()->id;
            $newChallenge->organization_id = $organizationId;
            $newChallenge->category_id = $challengeTemplateData->category_id;
            $newChallenge->duration_id = $challengeTemplateData->duration_id;
            $newChallenge->level_id = $challengeTemplateData->level_id;
            $newChallenge->slug = $slug;
            $newChallenge->title = $title;
            $newChallenge->description = $challengeTemplateData->description;
            $newChallenge->privacy = $challengeTemplateData->privacy;
            $newChallenge->media_type = $challengeTemplateData->media_type;
            $newChallenge->media = $challengeTemplateData->getRawOriginal('media');
            $newChallenge->status = $challengeTemplateData->status;
            $newChallenge->source_link = $challengeTemplateData->source_link;
            $newChallenge->agreement = $challengeTemplateData->agreement;
            $newChallenge->is_notification_enabled = $challengeTemplateData->is_notification_enabled;
            $newChallenge->project_privacy = $challengeTemplateData->project_privacy;
            $newChallenge->is_open = $challengeTemplateData->is_open;
            $newChallenge->is_auto_created = $challengeTemplateData->is_auto_created;
            $newChallenge->allow_winner_change = '0';
            $newChallenge->save();

            return $newChallenge;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteChallengeTemplate($slug, $challengeTemplateId)
    {
        try {
            $challengeTemplate = ChallengeTemplate::where('slug', $slug)->delete();
            if ($challengeTemplate) {
                $associatedChallengeTemplate = event(new DeleteChallengeTemplateAssociatedData($challengeTemplateId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCheckChallengeUuid($uuid)
    {
        try {
            return ChallengeTemplate::where('uuid', $uuid)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeTemplateBasedOnId($id)
    {
        try {
            return ChallengeTemplate::where('id', $id)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function addChallengeTemplateComponentAssociation($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeAssociations = ComponentAssociation::where('challenge_id', $challengeId)->get();
            if ($getChallengeAssociations->isNotEmpty()) {
                foreach ($getChallengeAssociations as $challengeAssociation) {
                    $newChallengeTemplateAssociation = new LabMarketplaceComponentAssociations();
                    $newChallengeTemplateAssociation->challenge_template_id = $templateChallengeId;
                    $newChallengeTemplateAssociation->sequence = $challengeAssociation->sequence;

                    // Commented for temporary current time being
                    // if ($challengeAssociation->lab_id != null) {
                    //     $newChallengeTemplateAssociation->lab_marketplace_id = $challengeAssociation->lab_id;
                    // } elseif ($challengeAssociation->lab_program_id != null) {
                    //     $newChallengeTemplateAssociation->lab_program_id = $challengeAssociation->lab_program_id;
                    // } elseif ($challengeAssociation->resource_module_id != null) {
                    //     $newChallengeTemplateAssociation->resource_module_id = $challengeAssociation->resource_module_id;
                    // } elseif ($challengeAssociation->resource_collection_id != null) {
                    //     $newChallengeTemplateAssociation->resource_collection_id = $challengeAssociation->resource_collection_id;
                    // } elseif ($challengeAssociation->resource_group_id != null) {
                    //     $newChallengeTemplateAssociation->resource_group_id = $challengeAssociation->resource_group_id;
                    // }

                    if ($challengeAssociation->resource_module_id != null) {
                        $newChallengeTemplateAssociation->resource_module_id = $challengeAssociation->resource_module_id;
                    } elseif ($challengeAssociation->resource_collection_id != null) {
                        $newChallengeTemplateAssociation->resource_collection_id = $challengeAssociation->resource_collection_id;
                    }
                    $newChallengeTemplateAssociation->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function redeemChallengeTemplateComponentAssociation($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateComponentAssociations = LabMarketplaceComponentAssociations::where('challenge_template_id', $challengeTemplateId)->get();
            if ($checkChallengeTemplateComponentAssociations->isNotEmpty()) {
                foreach ($checkChallengeTemplateComponentAssociations as $challengeTemplateComponentAssociation) {
                    $newChallengeAssociation = new ComponentAssociation();
                    $newChallengeAssociation->challenge_id = $redeemChallengeId;
                    $newChallengeAssociation->sequence = $challengeTemplateComponentAssociation->sequence;

                    // Commented for temporary current time being
                    // if ($challengeTemplateComponentAssociation->lab_marketplace_id != null) {
                    //     $newChallengeAssociation->lab_id = $challengeTemplateComponentAssociation->lab_marketplace_id;
                    // } elseif ($challengeTemplateComponentAssociation->lab_program_id != null) {
                    //     $newChallengeAssociation->lab_program_id = $challengeTemplateComponentAssociation->lab_program_id;
                    // } elseif ($challengeTemplateComponentAssociation->resource_module_id != null) {
                    //     $newChallengeAssociation->resource_module_id = $challengeTemplateComponentAssociation->resource_module_id;
                    // } elseif ($challengeTemplateComponentAssociation->resource_collection_id != null) {
                    //     $newChallengeAssociation->resource_collection_id = $challengeTemplateComponentAssociation->resource_collection_id;
                    // } elseif ($challengeTemplateComponentAssociation->resource_group_id != null) {
                    //     $newChallengeAssociation->resource_group_id = $challengeTemplateComponentAssociation->resource_group_id;
                    // }

                    if ($challengeTemplateComponentAssociation->resource_module_id != null) {
                        $newChallengeAssociation->resource_module_id = $challengeTemplateComponentAssociation->resource_module_id;
                    } elseif ($challengeTemplateComponentAssociation->resource_collection_id != null) {
                        $newChallengeAssociation->resource_collection_id = $challengeTemplateComponentAssociation->resource_collection_id;
                    }
                    $newChallengeAssociation->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteOrganizationChallengeTemplate($organizationId)
    {
        try {
            $fetchOrganizationChallengeTemplates = ChallengeTemplate::where('organization_id', $organizationId)->get();
            if (!empty($fetchOrganizationChallengeTemplates)) {
                foreach ($fetchOrganizationChallengeTemplates as $organizationChallengeTemplate) {
                    $deleteOrganizationChallengeTemplate = self::deleteChallengeTemplate($organizationChallengeTemplate->slug, $organizationChallengeTemplate->id);
                    if (!$deleteOrganizationChallengeTemplate) {
                        return false;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengesTemplate()
    {
        try {
            return ChallengeTemplate::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
