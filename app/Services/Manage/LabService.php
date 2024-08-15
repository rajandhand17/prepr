<?php

namespace App\Services\Manage;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabChallengeRedeem;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Database\Eloquent\Collection;

class LabService
{
    public function getLabCountBasedOnOrganization($organizationId)
    {
        try {
            $lab_count = Lab::where(['organization_id' => $organizationId, 'is_pre_built' => '0', 'is_auto_created' => '0'])->count();

            return $lab_count;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabList($request, $organization)
    {
        try {
            $lab_list = Lab::select()->where('organization_id', '=', $organization->id);

            $lab_list = self::filterLabList($lab_list, $request);

            return $lab_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterLabList($lab_list, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $lab_list = $lab_list->whereSearchFilter($request->search ?? '');
            }

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'deactivated' || $request->status == 'archived') ? '2' : '3'));
                $lab_list = $lab_list->where('labs.status', $status);
            } else {
                $lab_list = $lab_list->where('labs.status', '1');
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $lab_list = $lab_list->whereIn('labs.category_id', $request->category);
            }

            if ($request->has('type') && !empty($request->type)) {
                $typeFilterIds = LabTypeModesService::getLabType($request->type);
                $lab_list = $lab_list->whereIn('labs.id', $typeFilterIds);
            }

            if ($request->has('source') && !empty($request->source)) {
                $sourceLabIds = self::getLabBaseOnSource($request->source);
                $lab_list = $lab_list->whereIn('labs.id', $sourceLabIds);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $lab_list = $lab_list->orderBy('labs.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $lab_list = $lab_list->orderBy('labs.title', 'DESC');
                        break;
                    case 'creation_date':
                        $lab_list = $lab_list->orderBy('labs.created_at', 'ASC');
                        break;
                    default:
                        $lab_list = $lab_list->orderBy('labs.id', 'ASC');
                }
            }

            if ($request->has('privacy')) {
                $privacy = null;
                switch ($request->privacy) {
                    case 'yes':
                        $privacy = config('constants.lab_privacy.yes');
                        break;
                    case 'no':
                        $privacy = config('constants.lab_privacy.no');
                        break;
                    default:
                        $privacy = null;
                }
                if ($privacy != null) {
                    $lab_list = $lab_list->where('privacy', $privacy);
                }
            }

            return $lab_list;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabBaseOnSource($source)
    {
        try {
            $createdByYouLabIds = collect([]);
            $onboardingLabIds = collect([]);
            $clonedByYouLabIds = collect([]);
            $createdByOrgLabIds = collect([]);
            if (in_array('created_by_you', $source)) {
                $createdByYouLabIds = Lab::where(['user_id' => auth('api')->user()->id])->pluck('id');
            }
            if (in_array('onboarding_challenges', $source)) {
                $onboardingLabIds = Lab::where(['is_auto_created' => '1'])->pluck('id');
            }
            if (in_array('cloned_by_you', $source)) {
                $clonedByYouLabIds = Lab::where(['is_pre_built' => '1'])->pluck('id');
            }
            if (in_array('created_by_organizations', $source)) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                $createdByOrgLabIds = Lab::where(['organization_id' => $organization->id])->pluck('id');
            }

            $labsCollection = new Collection();
            $labsCollection = $labsCollection->concat($createdByYouLabIds);
            $labsCollection = $labsCollection->concat($onboardingLabIds);
            $labsCollection = $labsCollection->concat($clonedByYouLabIds);
            $labsCollection = $labsCollection->concat($createdByOrgLabIds);

            return $labsCollection;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabBasedOnSlug($slug)
    {
        try {
            return Lab::where('slug', $slug)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getSourceByLabId($labId)
    {
        try {
            if (Lab::where(['id' => $labId, 'user_id' => auth('api')->user()->id])->exists()) {
                $source = 'created_by_you';
            } elseif (Lab::where(['id' => $labId, 'is_auto_created' => '1'])->exists()) {
                $source = 'onboarding_challenges';
            } elseif (Lab::where(['id' => $labId, 'is_pre_built' => '1', 'user_id' => auth('api')->user()->id])->exists()) {
                $source = 'cloned_by_you';
            } else {
                $source = 'created_by_organizations';
            }

            return $source;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabBasedOnId($Id)
    {
        try {
            return Lab::where('id', $Id)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadLabCoverImage($image)
    {
        try {
            $upload_lab_cover_image = FileUploadHelper::uploadImageToS3($image, 'lab');
            if ($upload_lab_cover_image == false) {
                return false;
            }

            return $upload_lab_cover_image;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLab($request, $upload_cover_image, $organizationId)
    {
        try {
            $status = config('constants.lab_status.draft');
            switch ($request->request_type) {
                case 'draft':
                    $status = config('constants.lab_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.lab_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.lab_status.archive');
                    break;
                default:
                    $status = config('constants.lab_status.draft');
                    break;
            }

            $type = config('constants.lab_type.na');
            switch ($request->type) {
                case 'assess':
                    $type = config('constants.lab_type.assess');
                    break;
                case 'onboard':
                    $type = config('constants.lab_type.onboard');
                    break;
                case 'engage':
                    $type = config('constants.lab_type.engage');
                    break;
                case 'grow':
                    $type = config('constants.lab_type.grow');
                    break;
                default:
                    $type = config('constants.lab_type.na');
                    break;
            }

            $privacy = config('constants.lab_privacy.no');
            switch ($request->privacy) {
                case 'yes':
                    $privacy = config('constants.lab_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.lab_privacy.no');
                    break;
                default:
                    $privacy = config('constants.lab_privacy.yes');
                    break;
            }

            $is_ai_created = config('constants.challenge_ai_created.no');
            if ($request->has('is_ai_created')) {
                switch ($request->is_ai_created) {
                    case 'yes':
                        $is_ai_created = config('constants.challenge_ai_created.yes');
                        break;
                    case 'no':
                        $is_ai_created = config('constants.challenge_ai_created.no');
                        break;
                    default:
                        $is_ai_created = config('constants.challenge_ai_created.no');
                        break;
                }
            }

            $model = new Lab();
            $slug = UtilityHelper::generateSlug($request->title, $model);

            $campusConnectStatus = $request->get('integrate_campus_connect', 'no');
            $lab = new Lab();
            $lab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $lab->language = $request->language;
            $lab->user_id = auth()->user()->id;
            $lab->organization_id = $organizationId;
            $lab->category_id = $request->category_id;
            $lab->duration_id = $request->duration_id;
            $lab->level_id = $request->level_id;
            $lab->type = $type;
            $lab->slug = $slug;
            $lab->title = $request->title;
            $lab->description = $request->description;
            $lab->privacy = $privacy;
            $lab->media_type = $request->media_type;
            $lab->media = $upload_cover_image;
            $lab->status = $status;
            $lab->total_share = 0;
            $lab->is_auto_created = '0';
            $lab->is_ai_created = $is_ai_created;
            $lab->is_resource_sequential = ($request->is_resource_sequential == 'yes') ? '1' : '0';
            $lab->is_sequential = ($request->is_sequential == 'yes') ? '1' : '0';
            $lab->is_achievement_enabled = ($request->is_achievement_enabled == 'yes') ? '1' : '0';
            $lab->is_notification_enabled = ($request->is_notification_enabled == 'yes') ? '1' : '0';
            $lab->is_verified = '0';
            $lab->is_live_event_enabled = $request->get('is_live_event_enabled') === 'yes' ? true : false;
            $lab->campus_connect_status = config('constants.campus_connect_status.'.$campusConnectStatus);
            $lab->is_accessible ='1';
            $lab->save();

            return $lab;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabUsingAI($request, $upload_cover_image, $organization)
    {
        $status = config('constants.lab_status.publish');
        $type = config('constants.lab_type.na');
        $privacy = config('constants.lab_privacy.yes');

        $is_ai_created = config('constants.challenge_ai_created.no');
        if ($request->has('is_ai_created')) {
            switch ($request->is_ai_created) {
                case 'yes':
                    $is_ai_created = config('constants.challenge_ai_created.yes');
                    break;
                case 'no':
                    $is_ai_created = config('constants.challenge_ai_created.no');
                    break;
                default:
                    $is_ai_created = config('constants.challenge_ai_created.no');
                    break;
            }
        }

        $model = new Lab();
        $slug = UtilityHelper::generateSlug($request->labTitle, $model);

        $lab = new Lab();
        $lab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
        $lab->language = $request->language;
        $lab->user_id = auth()->user()->id;
        $lab->organization_id = $organization->id;
        $lab->category_id = $request->category_id;
        $lab->duration_id = $request->duration_id;
        $lab->level_id = $request->level_id;
        $lab->type = $type;
        $lab->slug = $slug;
        $lab->title = $request->labTitle;
        $lab->description = $request->labDescription;
        $lab->privacy = $privacy;
        $lab->media_type = 'image';
        $lab->media = $upload_cover_image;
        $lab->status = $status;
        $lab->total_share = 0;
        $lab->is_auto_created = '0';
        $lab->is_ai_created = $is_ai_created;
        $lab->is_resource_sequential = '0';
        $lab->is_sequential = '0';
        $lab->is_achievement_enabled = '0';
        $lab->is_notification_enabled = '0';
        $lab->is_verified = '0';
        $lab->save();

        return $lab;
    }

    public static function updateLab($slug, $request, $upload_cover_image, $organizationData)
    {
        try {
            $lab = Lab::where('slug', $slug)->first();
            if ($lab !== null) {
                $privacy = $lab->privacy;
                if ($request->has('privacy')) {
                    switch ($request->privacy) {
                        case 'yes':
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                        case 'no':
                            $privacy = config('constants.lab_privacy.no');
                            break;
                        default:
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                    }
                }

                $type = config('constants.lab_type.na');
                switch ($request->type) {
                    case 'assess':
                        $type = config('constants.lab_type.assess');
                        break;
                    case 'onboard':
                        $type = config('constants.lab_type.onboard');
                        break;
                    case 'engage':
                        $type = config('constants.lab_type.engage');
                        break;
                    case 'grow':
                        $type = config('constants.lab_type.grow');
                        break;
                    default:
                        $type = config('constants.lab_type.na');
                        break;
                }

                $campusConnectStatus = $request->get('integrate_campus_connect', 'no');
                $lab->language = ($request->has('language')) ? $request->language : $lab->language;
                $lab->organization_id = $organizationData->id;
                $lab->category_id = ($request->has('category_id')) ? $request->category_id : $lab->category_id;
                $lab->duration_id = ($request->has('duration_id')) ? $request->duration_id : $lab->duration_id;
                $lab->level_id = ($request->has('level_id')) ? $request->level_id : $lab->level_id;
                $lab->title = ($request->has('title')) ? $request->title : $lab->title;
                $lab->description = ($request->has('description')) ? $request->description : $lab->description;
                $lab->type = $type;
                $lab->privacy = $privacy;
                $lab->media_type = $request->media_type;
                $lab->media = ($upload_cover_image != null) ? $upload_cover_image : $lab->cover_image;
                $lab->status = ($request->request_type == 'draft') ? '0' : (($request->request_type == 'publish') ? '1' : '2');
                $lab->is_resource_sequential = ($request->has('is_resource_sequential')) ? (($request->is_resource_sequential == 'yes') ? '1' : '0') : $lab->is_resource_sequential;
                $lab->is_sequential = ($request->has('is_sequential')) ? (($request->is_sequential == 'yes') ? '1' : '0') : $lab->is_sequential;
                $lab->is_achievement_enabled = ($request->has('is_achievement_enabled')) ? (($request->is_achievement_enabled == 'yes') ? '1' : '0') : $lab->is_achievement_enabled;
                $lab->is_notification_enabled = ($request->has('is_achievement_enabled')) ? (($request->is_notification_enabled == 'yes') ? '1' : '0') : $lab->is_achievement_enabled;
                $lab->is_live_event_enabled = $request->get('is_live_event_enabled') === 'yes' ? true : false;
                $lab->campus_connect_status = config('constants.campus_connect_status.'.$campusConnectStatus);
                $lab->is_accessible ='1';
                $lab->save();

                return $lab;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteLab($lab_id)
    {
        try {
            $lab = Lab::find($lab_id)->delete();
            if ($lab) {
                $associatedLabs = event(new DeleteLabAssociatedData($lab_id));

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkSlug($slug)
    {
        try {
            $checklab = Lab::where('slug', $slug)->first();
            if ($checklab) {
                return $checklab;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $checklabName = Lab::where('title', $title)->first();
            if ($checklabName) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabListName($request, $organization)
    {
        try {
            $lab_list = Lab::select('uuid', 'title', 'media')->where(['organization_id' => $organization->id, 'is_accessible' => '1']);
            $lab_list = self::filterLabList($lab_list, $request);
            $limit = config('site-settings.listing_limit');

            return $lab_list->limit($limit)->get();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabIdBasedOnUUIDArray($uuid)
    {
        try {
            $lab = Lab::whereIn('uuid', $uuid)->pluck('id')->all();
            if ($lab != null) {
                return $lab;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabIdBasedOnId($id)
    {
        try {
            $lab = Lab::whereIn('id', $id)->pluck('id')->all();
            if ($lab != null) {
                return $lab;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updatePreBuilt($id, $is_pre_built)
    {
        try {
            $lab = Lab::find($id);
            $lab->is_pre_built = $is_pre_built;
            $lab->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function labMarketplaceUpdatePreBuilt($labMarketplaceId)
    {
        try {
            $labMarketplaceData = LabChallengeRedeem::where(['lab_marketplace_id' => $labMarketplaceId, 'is_redeemed' => '0'])->first();
            if ($labMarketplaceData) {
                $labUpdate = Lab::find($labMarketplaceData->lab_id);
                if ($labUpdate) {
                    $labUpdate->is_pre_built = '0';
                    $labUpdate->save();
                    if ($labMarketplaceData->delete()) {
                        return true;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabBasedOnUUID($uUID)
    {
        try {
            return Lab::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where('UUID', $uUID)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabBasedOnSkills($skills)
    {
        try {
            $getLabIdsBasedOnSkills = LabSkillsGroupsStackService::getLabIdBasedOnSkill([$skills]);
            $labs = Lab::whereIn('id', $getLabIdsBasedOnSkills)->take(config('site-settings.skills_par_module_limit'))->get();

            return $labs;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function labUserInviteCount($organizationId)
    {
        try {
            $getLabAcceptedMembersBasedOnIds = [];
            $getLabBasedOnOrganization = Lab::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->pluck('id');
            $getLabAcceptedMembersBasedOnIds = MemberManagementService::getComponentAcceptedMembersBasedOnIds($getLabBasedOnOrganization, 'lab');

            return $getLabAcceptedMembersBasedOnIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteOrganizationLab($organizationId)
    {
        try {
            $fetchOrganizationLabs = Lab::where('organization_id', $organizationId)->pluck('id');
            if (!empty($fetchOrganizationLabs)) {
                foreach ($fetchOrganizationLabs as $organizationLab) {
                    $deleteOrganizationLab = self::deleteLab($organizationLab);
                    if (!$deleteOrganizationLab) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabsBasedOnOrganizationId($organizationId)
    {
        try {
            return Lab::query()->where('organization_id', $organizationId)->orderBy('id', 'desc')->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getLabBasedOnUserId($userId)
    {
        try {
            return Lab::query()->where('user_id', $userId)->get();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getLabsBasedOnIds($ids)
    {
        try {
            return Lab::query()->whereIn('id', $ids)->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function fetchLabReportBasedOnOrganization($organizationId)
    {
        try {
            $fetchLabs = Lab::where(['organization_id' => $organizationId, 'status' => '1', 'is_accessible' => '1'])->get();

            return $fetchLabs;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneLab($labId, $organization)
    {
        try {
            $originalLab = Lab::find($labId);
            $model = new Lab();
            $slug = UtilityHelper::generateSlug($organization->title.' '.$originalLab->title, $model);
            $clonedLab = $originalLab->replicate();
            $clonedLab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $clonedLab->user_id = auth()->user()->id;
            $clonedLab->organization_id = $organization->id;
            $clonedLab->slug = $slug;
            $clonedLab->save();

            return $clonedLab;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
