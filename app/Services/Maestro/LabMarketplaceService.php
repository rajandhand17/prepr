<?php

namespace App\Services\Maestro;

use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabMarketplace;
use Exception;

class LabMarketplaceService
{
    public static function getLabMarketplace()
    {
        try {
            return LabMarketplace::where('language', \Session::get('globalLocale') ? \Session::get('globalLocale') : 'en')->orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabMarketplaceBasedOnId($id)
    {
        try {
            return LabMarketplace::where('id', $id)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteLabMarketplace($slug, $labMarketplaceId)
    {
        try {
            // Deleting lab marketplace
            $labMarketplace = LabMarketplace::where('slug', $slug)->delete();
            if ($labMarketplace) {
                // Triggered LabMarketplace related data deletion event
                event(new DeleteLabMarketplaceAssociatedData($labMarketplaceId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addLabToMarketplace($slug)
    {
        try {
            $existsLabs = Lab::where('slug', $slug)->first();
            if ($existsLabs != null) {
                $labTemplate = new LabMarketplace();
                $labTemplate->uuid = $existsLabs->uuid;
                $labTemplate->language = $existsLabs->language;
                $labTemplate->user_id = auth()->user()->id;
                $labTemplate->organization_id = $existsLabs->organization_id;
                $labTemplate->category_id = $existsLabs->category_id;
                $labTemplate->duration_id = $existsLabs->duration_id;
                $labTemplate->level_id = $existsLabs->level_id;
                $labTemplate->type = $existsLabs->type;
                $labTemplate->slug = $existsLabs->slug;
                $labTemplate->title = $existsLabs->title;
                $labTemplate->description = $existsLabs->description;
                $labTemplate->privacy = $existsLabs->privacy;
                $labTemplate->media_type = $existsLabs->media_type;
                $labTemplate->media = $existsLabs->getRawOriginal('media');
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
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getList($getPreSelectedLabTemplates, $language)
    {
        try {
            return LabMarketplace::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $language)->orderBy('id', 'DESC')->pluck('title', 'id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabMarketplaceList($request)
    {
        try {
            $searched = $request->search;
            $modules = LabMarketplace::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language);
            if (!empty($searched)) {
                $modules = $modules->where('title', 'like', '%'.$searched.'%');
            }

            return $modules->pluck('title', 'id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
