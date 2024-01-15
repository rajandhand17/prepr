<?php

namespace App\Services\Manage;

use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
use App\Events\Labs\DeleteLabAssociatedData;
use App\Models\Lab;
use App\Models\LabMarketplace;
use App\Models\LabTemplate;

class LabMarketplaceService
{
    public static function createLabMarketplace($slug,$organizationId)
    {
        try {
            $existsLabs = Lab::where('slug', $slug)->first();

            if ($existsLabs != null) {
                $labTemplate = new LabMarketplace();
                $labTemplate->uuid = $existsLabs->uuid;
                $labTemplate->language = $existsLabs->language;
                $labTemplate->user_id = auth()->user()->id;
                $labTemplate->organization_id = $organizationId;
                $labTemplate->category_id = $existsLabs->category_id;
                $labTemplate->duration_id = $existsLabs->duration_id;
                $labTemplate->level_id = $existsLabs->level_id;
                $labTemplate->type = $existsLabs->type;
                $labTemplate->slug = $existsLabs->slug;
                $labTemplate->title = $existsLabs->title;
                $labTemplate->description = $existsLabs->description;
                $labTemplate->privacy = $existsLabs->privacy;
                $labTemplate->media_type = $existsLabs->media_type;
                $labTemplate->media = $existsLabs->media;
                $labTemplate->status = $existsLabs->status;
                $labTemplate->total_share = $existsLabs->total_share;
                $labTemplate->is_auto_created = $existsLabs->is_auto_created;
                $labTemplate->is_resource_sequential = $existsLabs->is_resource_sequential;
                $labTemplate->is_sequential = $existsLabs->is_sequential;
                $labTemplate->is_achievement_enabled = $existsLabs->is_achievement_enabled;
                $labTemplate->is_notification_enabled = $existsLabs->is_notification_enabled;
                $labTemplate->is_verified = $existsLabs->is_verified;
                $labTemplate->save();
                return $labTemplate;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getCheckUuid($uuid)
    {
        try {
            return LabMarketplace::where('uuid', $uuid)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabMarketplaceBasedOnSlug($slug){
        try {
            return LabMarketplace::where('slug', $slug)->first();
        }catch (\Exception $e) {
            return false;
        }
    }

    public function deleteLabMarketplace($slug,$labMarketplaceId){
        try {
            $labMarketplace=LabMarketplace::where("slug",$slug)->delete();
            if($labMarketplace){
                $associatedLabMarketplace=event(new DeleteLabMarketplaceAssociatedData($labMarketplaceId));
                return true;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }
}
