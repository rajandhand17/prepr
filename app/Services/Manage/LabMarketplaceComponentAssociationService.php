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
                if ($componentAssociation->challenge_id !== '') {
                    $getChallenges = Challenge::where('id', $componentAssociation->challenge_id)->first();
                    $challengesTemplate=$this->challengeTemplateRepository->createTemplateChallenge($getChallenges->id, $getChallenges->organization_id);
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
                $labSkillsGroupsStack->template_lab_id = $componentAssociation->lab_program_id;
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
