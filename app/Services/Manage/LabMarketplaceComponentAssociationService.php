<?php

namespace App\Services\Manage;

use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ChallengePathTemplate;
use App\Models\ComponentAssociation;
use App\Models\LabMarketplaceComponentAssociations;
use App\Models\LabMarketplaceProgram;
use App\Models\LabProgram;
use App\Models\TemplateChallenge;
use App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateRepository;

class LabMarketplaceComponentAssociationService
{
    private $challengeTemplateRepository;
    public function __construct(ChallengeTemplateRepository $challengeTemplateRepository){
        $this->challengeTemplateRepository = $challengeTemplateRepository;
    }
    public function createMarketplaceComponentAssociation($labMarketplaceId, $labId)
    {
        try {
            $componentAssociations = ComponentAssociation::where('lab_id', $labId)->get();
            foreach ($componentAssociations as $componentAssociation){
                if ($componentAssociation->lab_program_id !== '') {
                    $labProgram = LabProgram::where('id', $componentAssociation->lab_program_id)->first();
                    $labMarketplaceProgram = new LabMarketplaceProgram();
                    $labMarketplaceProgram->language = $labProgram->language;
                    $labMarketplaceProgram->title = $labProgram->title;
                    $labMarketplaceProgram->slug = $labProgram->slug;
                    $labMarketplaceProgram->description = $labProgram->description;
                    $labMarketplaceProgram->organization_id = $labProgram->organization_id;
                    $labMarketplaceProgram->category_id = $labProgram->category_id;
                    $labMarketplaceProgram->duration_id = $labProgram->duration_id;
                    $labMarketplaceProgram->level_id = $labProgram->level_id;
                    $labMarketplaceProgram->user_id = $labProgram->user_id;
                    $labMarketplaceProgram->media_type = $labProgram->media_type;
                    $labMarketplaceProgram->media = $labProgram->upload_media;
                    $labMarketplaceProgram->privacy = $labProgram->privacy;
                    $labMarketplaceProgram->status = $labProgram->status;
                    $labMarketplaceProgram->is_auto_created = $labProgram->is_auto_created;
                    $labMarketplaceProgram->is_sequential = $labProgram->is_sequential;
                    $labMarketplaceProgram->is_achievement_enabled = $labProgram->is_achievement_enabled;
                    $labMarketplaceProgram->save();
                }

                if ($componentAssociation->challenge_id !== '') {
                    $getChallenges = Challenge::where('id', $componentAssociation->challenge_id)->first();
                    $challengesTemplate=$this->challengeTemplateRepository->createTemplateChallenge($getChallenges->id, $getChallenges->organization_id);
//                    $challengesTemplate = new TemplateChallenge();
//                    $challengesTemplate->uuid = $getChallenges->uuid;
//                    $challengesTemplate->language = $getChallenges->language;
//                    $challengesTemplate->user_id = $getChallenges->user_id;
//                    $challengesTemplate->organization_id = $getChallenges->organization_id;
//                    $challengesTemplate->category_id = $getChallenges->category_id;
//                    $challengesTemplate->duration_id = $getChallenges->duration_id;
//                    $challengesTemplate->level_id = $getChallenges->level_id;
//                    $challengesTemplate->slug = $getChallenges->slug;
//                    $challengesTemplate->title = $getChallenges->title;
//                    $challengesTemplate->description = $getChallenges->description;
//                    $challengesTemplate->privacy = $getChallenges->privacy;
//                    $challengesTemplate->media_type = $getChallenges->media_type;
//                    $challengesTemplate->media = $getChallenges->media;
//                    $challengesTemplate->status = $getChallenges->status;
//                    $challengesTemplate->source_link = $getChallenges->source_link;
//                    $challengesTemplate->agreement = $getChallenges->agreement;
//                    $challengesTemplate->is_notification_enabled = $getChallenges->is_notification_enabled;
//                    $challengesTemplate->project_privacy = $getChallenges->project_privacy;
//                    $challengesTemplate->is_pre_built = $getChallenges->is_pre_built;
//                    $challengesTemplate->is_open = $getChallenges->is_open;
//                    $challengesTemplate->is_auto_created = $getChallenges->is_auto_created;
//                    $challengesTemplate->save();
                }

                if ($componentAssociation->challenge_path_id !== '') {
                    $chllengePath = ChallengePath::where('id', $componentAssociation->id)->first();
                    $challengesPathTemplate = new ChallengePathTemplate();
                    $challengesPathTemplate->uuid = $chllengePath->uuid;
                    $challengesPathTemplate->language = $chllengePath->language;
                    $challengesPathTemplate->title = $chllengePath->title;
                    $challengesPathTemplate->slug = $chllengePath->slug;
                    $challengesPathTemplate->description = $chllengePath->description;
                    $challengesPathTemplate->user_id = $chllengePath->user_id;
                    $challengesPathTemplate->organization_id = $chllengePath->organization_id;
                    $challengesPathTemplate->category_id = $chllengePath->category_id;
                    $challengesPathTemplate->duration_id = $chllengePath->duration_id;
                    $challengesPathTemplate->level_id = $chllengePath->level_id;
                    $challengesPathTemplate->media_type = $chllengePath->media_type;
                    $challengesPathTemplate->media = $chllengePath->media;
                    $challengesPathTemplate->privacy = $chllengePath->privacy;
                    $challengesPathTemplate->status = $chllengePath->status;
                    $challengesPathTemplate->is_achievement_enabled = $chllengePath->is_achievement_enabled;
                    $challengesPathTemplate->is_sequential = $chllengePath->is_sequential;
                    $challengesPathTemplate->is_auto_created = $chllengePath->is_auto_created;
                    $challengesPathTemplate->save();
                }
                $labSkillsGroupsStack = new LabMarketplaceComponentAssociations();
                $labSkillsGroupsStack->template_lab_id = $labMarketplaceProgram->id;
                $labSkillsGroupsStack->lab_marketplace_id = $labMarketplaceId;
                $labSkillsGroupsStack->template_challenge_id = $challengesTemplate->id;
                $labSkillsGroupsStack->template_challenge_path_id = $challengesPathTemplate->challenge_path_id;
                $labSkillsGroupsStack->template_resource_module_id = $componentAssociation->resource_module_id;
                $labSkillsGroupsStack->template_resource_collection_id = $componentAssociation->resource_collection_id;
                $labSkillsGroupsStack->template_resource_group_id = $componentAssociation->resource_group_id;
                $labSkillsGroupsStack->sequence = $componentAssociation->sequence;
                $labSkillsGroupsStack->save();
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteLabMarketplaceComponentAssociation($labMarketplaceId)
    {
        try {
            $labMarketplaceComponentAssociations = LabMarketplaceComponentAssociations::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($labMarketplaceComponentAssociations) {
                $deleteLabMarketplaceComponentAssociation = LabMarketplaceComponentAssociations::where('lab_marketplace_id', $labMarketplaceId)->delete();
                if (!$deleteLabMarketplaceComponentAssociation) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
