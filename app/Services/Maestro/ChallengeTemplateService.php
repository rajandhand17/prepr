<?php

namespace App\Services\Maestro;

use App\Events\ChallengeTemplate\DeleteChallengeTemplateAssociatedData;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengeTemplate;
use App\Models\ComponentAssociation;
use App\Models\LabMarketplaceComponentAssociations;
use Exception;

class ChallengeTemplateService
{
    public static function getChallengesTemplate()
    {
        try {
            return ChallengeTemplate::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getCheckChallengeUuid($uuid)
    {
        try {
            return ChallengeTemplate::where('uuid', $uuid)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeTemplateBasedOnId($id)
    {
        try {
            return ChallengeTemplate::where('id', $id)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengeTemplate($slug, $challengeTemplateId)
    {
        try {
            $challengeTemplate = ChallengeTemplate::where('slug', $slug)->delete();
            if ($challengeTemplate) {
                event(new DeleteChallengeTemplateAssociatedData($challengeTemplateId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeTemplateBasedOnUuid($uuid)
    {
        try {
            return ChallengeTemplate::where('uuid', $uuid)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createChallengeTemplate($challengeId)
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
            $templateChallenge->description_type = $originalChallenge->description_type;
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
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function addChallengeTemplateComponentAssociation($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeAssociations = ComponentAssociation::where('challenge_id', $challengeId)->get();
            if ($getChallengeAssociations->isNotEmpty()) {
                foreach ($getChallengeAssociations as $challengeAssociation) {
                    $newChallengeTemplateAssociation = new LabMarketplaceComponentAssociations();
                    $newChallengeTemplateAssociation->challenge_template_id = $templateChallengeId;
                    $newChallengeTemplateAssociation->sequence = $challengeAssociation->sequence;
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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getList($getPreSelectedLabTemplates, $language)
    {
        try {
            return ChallengeTemplate::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $language)->orderBy('id', 'DESC')->pluck('title', 'id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengesTemplateList($request)
    {
        try {
            $searched = $request->search;
            $modules = ChallengeTemplate::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language);
            if (!empty($searched)) {
                $modules = $modules->where('title', 'like', '%'.$searched.'%');
            }
            $modules = $modules->pluck('title', 'id');

            return $modules;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
