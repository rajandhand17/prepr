<?php

namespace App\Services\Manage;

use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
use App\Models\Lab;
use App\Models\LabMarketplace;
use Exception;

class LabMarketplaceService
{
    public static function getLabMarketPlaceList($request)
    {
        try {
            $lab_marketplace_list = LabMarketplace::select();

            $lab_marketplace_list = self::filterLabList($lab_marketplace_list, $request);

            return $lab_marketplace_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            return false;
        }
    }

    public static function filterLabList($lab_marketplace_list, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $lab_marketplace_list = $lab_marketplace_list->where('lab_marketplace.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $lab_marketplace_list = $lab_marketplace_list->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $lab_marketplace_list = $lab_marketplace_list->whereIn('level_id', $request->level_id);
            }

            if ($request->has('organization_id') && !empty($request->organization_id) && is_array($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                if (!empty($getOrganizationIds)) {
                    $lab_marketplace_list = $lab_marketplace_list->whereIn('organization_id', $getOrganizationIds);
                }
            }

            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $lab_marketplace_list = $lab_marketplace_list->whereIn('lab_marketplace.id', function ($query) use ($request) {
                    $query->select('lab_marketplace_skills_groups_stack.lab_marketplace_id')
                    ->from('lab_marketplace_skills_groups_stack')
                    ->whereIn('lab_marketplace_skills_groups_stack.foreign_id', $request->skills)
                        ->where('lab_marketplace_skills_groups_stack.type', '0')
                        ->whereNull('lab_marketplace_skills_groups_stack.deleted_at')
                        ->distinct();
                })->distinct('lab_marketplace.uuid');
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $lab_marketplace_list = $lab_marketplace_list->orderBy('lab_marketplace.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $lab_marketplace_list = $lab_marketplace_list->orderBy('lab_marketplace.title', 'DESC');
                        break;
                    case 'creation_date':
                        $lab_marketplace_list = $lab_marketplace_list->orderBy('lab_marketplace.created_at', 'ASC');
                        break;
                    default:
                        $lab_marketplace_list = $lab_marketplace_list->orderBy('lab_marketplace.id', 'ASC');
                }
            }

            return $lab_marketplace_list;
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCheckLabUuid($uuid)
    {
        try {
            return LabMarketplace::where('uuid', $uuid)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getLabMarketplaceBasedOnSlug($slug)
    {
        try {
            return LabMarketplace::where('slug', $slug)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteLabMarketplace($slug, $labMarketplaceId)
    {
        try {
            $labMarketplace = LabMarketplace::where('slug', $slug)->delete();
            if ($labMarketplace) {
                $associatedLabMarketplace = event(new DeleteLabMarketplaceAssociatedData($labMarketplaceId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
