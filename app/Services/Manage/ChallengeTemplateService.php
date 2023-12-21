<?php

namespace App\Services\Manage;


use App\Models\Challenge;
use App\Models\TemplateChallenge;

class ChallengeTemplateService
{
    public function createTemplateChallenge($challengeId, $organization)
    {
        try {
            $originalChallenge = Challenge::find($challengeId);
            $templateChallenge = new TemplateChallenge();
            $templateChallenge->uuid = $originalChallenge->uuid;
            $templateChallenge->title = $originalChallenge->title;
            $templateChallenge->slug = $originalChallenge->slug;
            $templateChallenge->user_id = $originalChallenge->user_id;
            $templateChallenge->organization_id = $originalChallenge->organization_id;
            $templateChallenge->category_id = $originalChallenge->category_id;
            $templateChallenge->duration_id = $originalChallenge->duration_id;
            $templateChallenge->level_id = $originalChallenge->level_id;
            $templateChallenge->description = $originalChallenge->description;
            $templateChallenge->privacy = $originalChallenge->privacy;
            $templateChallenge->media_type = $originalChallenge->media_type;
            $templateChallenge->media = $originalChallenge->media;
            $templateChallenge->status = $originalChallenge->status;
            $templateChallenge->source_link = $originalChallenge->source_link;
            $templateChallenge->agreement = $originalChallenge->agreement;
            $templateChallenge->is_notification_enabled = $originalChallenge->is_notification_enabled;
            $templateChallenge->project_privacy = $originalChallenge->project_privacy;
            $templateChallenge->is_open = $originalChallenge->is_open;
            $templateChallenge->is_auto_created = $originalChallenge->is_auto_created;
            $templateChallenge->save();
            return $templateChallenge;
        } catch (\Exception $e) {
            return false;
        }
    }
}
