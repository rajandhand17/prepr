<?php

namespace App\Services\Manage;

use App\Events\LabMarketplace\DeleteLabMarketplaceAssociatedData;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabChallengeRedeem;
use App\Models\LabMarketplace;
use App\Models\Organization;
use Exception;
use HiFolks\RandoPhp\Randomize;

class LabMarketplaceService
{
    public static function getLabMarketPlaceList($request)
    {
        try {
            $lab_marketplace_list = LabMarketplace::select()->where('language', $request->language);

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

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $lab_marketplace_list = $lab_marketplace_list->whereIn('lab_marketplace.category_id', $request->category);
            }

            if ($request->has('organization_ids') && !empty($request->organization_ids) && is_array($request->organization_ids)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_ids)->pluck('id');
                $lab_marketplace_list = $lab_marketplace_list->whereIn('organization_id', $getOrganizationIds);
            }

            if ($request->has('status') && !empty($request->status)) {
                $getLabRedeemedIds = LabChallengeRedeem::where(['organization_id' => $request->organization_id, 'is_redeemed' => '1'])->whereNotNull('lab_id')->pluck('lab_marketplace_id');
                switch ($request->status) {
                    case 'redeemed':
                        $lab_marketplace_list = $lab_marketplace_list->whereIn('id', $getLabRedeemedIds);
                        break;
                    case 'not_redeemed':
                        $lab_marketplace_list = $lab_marketplace_list->whereNotIn('id', $getLabRedeemedIds);
                        break;
                    default:
                        $lab_marketplace_list = $lab_marketplace_list;
                        break;
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

    public static function deleteLabMarketplace($slug, $labMarketplaceId)
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

    public static function checkLabRedeemedOrNot($labMarketplaceId, $organizationId)
    {
        try {
            $checkLabRedeemed = LabChallengeRedeem::where(['lab_marketplace_id' => $labMarketplaceId, 'organization_id' => $organizationId, 'is_redeemed' => '1'])->exists();
            if (!$checkLabRedeemed) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function redeemLabMarketplaceToLab($labMarketplaceId, $organizationId)
    {
        try {
            $labMarketplaceData = LabMarketplace::find($labMarketplaceId);
            $organisationName = Organization::where('id', $organizationId)->pluck('title')->first();

            $model = new Lab();
            $slug = UtilityHelper::generateSlug($organisationName.'-'.$labMarketplaceData->slug, $model);

            $title = $title_format = $organisationName.' '.$labMarketplaceData->title;
            $next = 1;
            while (Lab::where('title', '=', $title)->first()) {
                $title = "{$title_format} {$next}";
                $next++;
            }

            $newLab = new Lab();
            $newLab->type = $labMarketplaceData->type;
            $newLab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $newLab->language = $labMarketplaceData->language;
            $newLab->user_id = auth()->user()->id;
            $newLab->organization_id = $organizationId;
            $newLab->category_id = $labMarketplaceData->category_id;
            $newLab->duration_id = $labMarketplaceData->duration_id;
            $newLab->level_id = $labMarketplaceData->level_id;
            $newLab->slug = $slug;
            $newLab->title = $title;
            $newLab->description = $labMarketplaceData->description;
            $newLab->privacy = $labMarketplaceData->privacy;
            $newLab->media_type = $labMarketplaceData->media_type;
            $newLab->media = $labMarketplaceData->getRawOriginal('media');
            $newLab->status = $labMarketplaceData->status;
            $newLab->total_share = '0';
            $newLab->is_auto_created = $labMarketplaceData->is_auto_created;
            $newLab->is_resource_sequential = $labMarketplaceData->is_resource_sequential;
            $newLab->is_sequential = $labMarketplaceData->is_sequential;
            $newLab->is_achievement_enabled = $labMarketplaceData->is_achievement_enabled;
            $newLab->is_notification_enabled = $labMarketplaceData->is_notification_enabled;
            $newLab->is_verified = $labMarketplaceData->is_verified;
            $newLab->save();

            return $newLab;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteOrganizationLabMarketPlace($organizationId)
    {
        try {
            $fetchOrganizationLabMarketPlaces = LabMarketplace::where('organization_id', $organizationId)->get();
            if (!empty($fetchOrganizationLabMarketPlaces)) {
                foreach ($fetchOrganizationLabMarketPlaces as $organizationLabMarketPlace) {
                    $deleteOrganizationLabMarketPlace = self::deleteLabMarketplace($organizationLabMarketPlace->slug, $organizationLabMarketPlace->id);
                    if (!$deleteOrganizationLabMarketPlace) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabMarketplace()
    {
        try {
            return LabMarketplace::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getLabMarketplaceBasedOnId($id)
    {
        try {
            return LabMarketplace::where('id',$id)->first();
        } catch (Exception $e) {
            return false;
        }
    }
}
