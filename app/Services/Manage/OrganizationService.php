<?php

namespace App\Services\Manage;

use App\Events\Organization\DeleteOrganizationAssociatedData;
use App\Helpers\ChargebeeHelper;
use App\Helpers\FileUploadHelper;
use App\Helpers\MixpanelHelper;
use App\Helpers\UtilityHelper;
use App\Jobs\Chargebee\SubscribePlanJob;
use App\Models\Organization;
use DB;
use HiFolks\RandoPhp\Randomize;

class OrganizationService
{
    public static function getOrganizationList($request)
    {
        try {
            $organization_list = Organization::select();

            $organization_list = self::filterOrganizationList($request, $organization_list);

            return $organization_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterOrganizationList($request, $organization_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $organization_list = $organization_list->where('organizations.title', 'like', '%'.$request->search.'%');
            }
            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'deactivated') ? '2' : '3'));
                $organization_list = $organization_list->where('organizations.status', $status);
            } else {
                $organization_list = $organization_list->where('organizations.status', '1');
            }

            if ($request->has('is_verified') && !empty($request->is_verified)) {
                $is_verified = ($request->is_verified == 'yes') ? '1' : '0';
                $organization_list = $organization_list->where('organizations.is_verified', $is_verified);
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $organization_list = $organization_list->whereIn('organizations.category', $request->category);
            }
            if ($request->has('organization_id') && !empty($request->organization_id) && is_array($request->organization_id)) {
                $organization_list = $organization_list->whereIn('organizations.uuid', $request->organization_id);
            }

            if ($request->has('owner') && !empty($request->owner)) {
                $organization_list = self::filterOrganizationBasedOnRoles($organization_list, $request);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $organization_list = $organization_list->orderBy('organizations.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $organization_list = $organization_list->orderBy('organizations.title', 'DESC');
                        break;
                    case 'creation_date':
                        $organization_list = $organization_list->orderBy('organizations.created_at', 'ASC');
                        break;
                    default:
                        $organization_list = $organization_list->orderBy('organizations.id', 'ASC');
                }
            }

            if ($request->has('plan') && !empty($request->plan)) {
                $getPlan = config('chargebee.chargebee_plan.'.$request->plan);
                if ($getPlan) {
                    $getOrganizationIds = ChargebeeSubscriptionService::getChargebeeBasedOnSubscription($request->plan);
                    $organization_list = $organization_list->whereIn('organizations.id', $getOrganizationIds);
                }
            }
            if ($request->has('total_employees') && !empty($request->total_employees)) {
                switch ($request->total_employees) {
                    case '1':
                        $organization_list = $organization_list->whereBetween('total_employees', [0, 50]);
                        break;
                    case '2':
                        $organization_list = $organization_list->whereBetween('total_employees', [51, 250]);
                        break;
                    case '3':
                        $organization_list = $organization_list->whereBetween('total_employees', [251, 1000]);
                        break;
                    case '4':
                        $organization_list = $organization_list->where('total_employees', '>=', 1000);
                        break;
                }
            }

            return $organization_list;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationBasedOnSlug($slug)
    {
        try {
            return Organization::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkOrganizationExistBasedOnTitle($request): bool
    {
        try {
            $organization_exists = Organization::select('id')->where('title', $request->title)->first();
            if ($organization_exists == null) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkOrganizationExistInTrashBasedOnTitle($request)
    {
        try {
            $organization_trashed_exists = Organization::select('id')->where('title', $request->title)->onlyTrashed()->first();
            if ($organization_trashed_exists == null) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadOrganizationProfileImage($request)
    {
        try {
            $profile_image_path = FileUploadHelper::uploadImageToS3($request->profile_image, 'organization');
            if (!$profile_image_path) {
                return false;
            }

            return $profile_image_path;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadOrganizationCoverImage($request)
    {
        try {
            $cover_image_path = FileUploadHelper::uploadImageToS3($request->cover_image, 'organization');
            if (!$cover_image_path) {
                return false;
            }

            return $cover_image_path;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createOrganization($request, $profile_image_path, $cover_image_path)
    {
        try {
            DB::beginTransaction();
            $model = new Organization();
            $organization = new Organization();
            $organization->language = isset($request->language) ? $request->language : 'en';
            $organization->user_id = auth()->user()->id;
            $organization->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $organization->title = $request->title;
            $organization->display_name = $request->title;
            $organization->description = isset($request->description) ? $request->description : null;
            $organization->slug = UtilityHelper::generateSlug($request->slug, $model);
            $organization->cover_image = $cover_image_path;
            $organization->profile_image = $profile_image_path;
            $organization->custom_url = $request->custom_url;
            $organization->website = isset($request->website) ? $request->website : null;
            $organization->about = isset($request->about) ? $request->about : null;
            $organization->category = $request->category;
            $organization->status = ($request->status == 'draft') ? '0' : (($request->status == 'publish') ? '1' : '3');
            $organization->total_employees = $request->total_employees;
            $organization->save();
            auth()->user()->attachRole('organization_owner', $organization);
            $request->name = $request->title;
            MixpanelHelper::mixpanel_tracking(config('mixpanel.create_org'), $request, auth()->user(), $request->ip());

            DB::commit();

            return $organization;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public static function updateOrganization($request, $cover_images_path, $profile_images_path, $slug)
    {
        try {
            DB::beginTransaction();
            $organization = Organization::where('slug', $slug)->first();
            if ($organization !== null) {
                $organization->language = ($request->has('language')) ? $request->language : $organization->language;
                $organization->title = ($request->has('title')) ? $request->title : $organization->title;
                $organization->display_name = ($request->has('display_name')) ? $request->title : $organization->display_name;
                $organization->description = ($request->has('description')) ? $request->description : $organization->description;
                $organization->cover_image = ($cover_images_path != null) ? $cover_images_path : $organization->cover_image;
                $organization->profile_image = ($profile_images_path != null) ? $profile_images_path : $organization->profile_image;
                $organization->custom_url = ($request->has('custom_url')) ? $request->custom_url : $organization->custom_url;
                $organization->website = ($request->has('website')) ? $request->website : $organization->website;
                $organization->about = ($request->has('about')) ? $request->about : $organization->about;
                $organization->category = ($request->has('category')) ? $request->category : $organization->category;
                $organization->status = ($request->has('status')) ? (($request->status == 'draft') ? '0' : (($request->status == 'publish') ? '1' : '3')) : $organization->status;
                $organization->total_employees = ($request->has('total_employees')) ? $request->total_employees : $organization->total_employees;
                $organization->save();
                DB::commit();

                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public static function deleteOrganization($organizationData, $request)
    {
        try {
            MixpanelHelper::mixpanel_tracking(config('mixpanel.delete_organization'), $organizationData, auth()->user(), $request->ip());
            $organization = Organization::find($organizationData->id)->delete();
            if ($organization) {
                event(new DeleteOrganizationAssociatedData($organizationData->id));

                return true;
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkSlug($slug)
    {
        try {
            $slug = Organization::where('slug', $slug)->first();
            if ($slug) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationExistBasedOnId($id)
    {
        try {
            $organization = Organization::find($id);
            if ($organization != null) {
                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationExistBasedOnUuid($uuid)
    {
        try {
            $organization = Organization::where('uuid', $uuid)->first();
            if ($organization != null) {
                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationExistBasedOnUuidArray($uuid)
    {
        try {
            $organization = Organization::select('id')->whereIn('uuid', $uuid)->get();
            if ($organization != null) {
                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationListOnlyNameAndUuid($request)
    {
        try {
            $organization_list = Organization::select();

            $organization_list = self::filterOrganizationList($request, $organization_list);

            return $organization_list->take(config('site-settings.dropdown_listing_limit'))->pluck('title', 'uuid');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterOrganizationBasedOnRoles($organization_list, $request)
    {
        $userRole = null;
        $invited_organization_ids = null;

        if (auth()->user()->hasRole('organization_owner')) {
            $userRole = 'Organization Owner';
        } elseif (auth()->user()->hasRole('organization_manager')) {
            $userRole = 'Organization Manager';
        } elseif (auth()->user()->hasRole('lab_manager')) {
            $userRole = 'Lab Manager';
        } elseif (auth()->user()->hasRole('challenge_manager')) {
            $userRole = 'Challenge Manager';
        } elseif (auth()->user()->hasRole('resource_manager')) {
            $userRole = 'Resource Manager';
        }

        if ($request->owner == 'invited' || $request->owner == 'all') {
            if ($userRole != null) {
                $invited_organization_ids = MemberManagementService::getFilteredMemberManagementList(
                    [
                        'module_type'   => '0',
                        'email'         => auth()->user()->email,
                        'role'          => $userRole,
                        'invite_status' => '1',
                    ]
                )->pluck('module_id');
            }
        }

        switch ($request->owner) {
            case 'invited':
                if ($invited_organization_ids != null) {
                    $organization_list = $organization_list->whereIn('organizations.id', $invited_organization_ids);
                }
                break;
            case 'my':
                $organization_list = $organization_list->where('organizations.user_id', auth()->user()->id);
                break;
            default:
                $owner_organization_ids = Organization::where('organizations.user_id', auth()->user()->id)->pluck('id');

                if ($invited_organization_ids != null) {
                    $final_organization_ids = $owner_organization_ids->merge($invited_organization_ids)->unique();
                } else {
                    $final_organization_ids = $owner_organization_ids;
                }

                $organization_list = $organization_list->whereIn('organizations.id', $final_organization_ids);
        }

        return $organization_list;
    }

    public function selectPlan($organization, $request)
    {
        try {
            switch ($request->plan_name) {
                case 'seed_plan_yearly':
                    $detailsPlan = config('chargebee.chargebee_plan.seed_plan_yearly');
                    break;
                case 'sprout_plan_yearly':
                    $detailsPlan = config('chargebee.chargebee_plan.sprout_plan_yearly');
                    break;
                case 'budd_plan_yearly':
                    $detailsPlan = config('chargebee.chargebee_plan.budd_plan_yearly');
                    break;
                case 'bloom_plan_yearly':
                    $detailsPlan = config('chargebee.chargebee_plan.bloom_plan_yearly');
                    break;
                case 'unlimited_plan':
                    $detailsPlan = config('chargebee.chargebee_plan.unlimited_plan');
                    break;
                default:
                    $detailsPlan = config('chargebee.chargebee_plan.seed_plan_yearly');
                    break;
            }
            $userData = auth()->user();
            dispatch(new SubscribePlanJob($userData, $organization, $detailsPlan));
            $checkLocalEntry = ChargebeeHelper::createChargebeePlanDetails($organization->id);
            if ($checkLocalEntry) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchOrganizationBasedOnUserId($userId)
    {
        try {
            return Organization::where('user_id', $userId)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function organizationUserInviteCount($organizationId)
    {
        try {
            $getOrganizationAcceptedMembersBasedOnIds = [];
            $getOrganizationBasedOnOrganization = Organization::where(['id' => $organizationId])->pluck('id');
            $getOrganizationAcceptedMembersBasedOnIds = MemberManagementService::getComponentAcceptedMembersBasedOnIds($getOrganizationBasedOnOrganization, 'organization');

            return $getOrganizationAcceptedMembersBasedOnIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function organizationManagerInviteCount($organizationId)
    {
        try {
            $getOrganizationAcceptedManagerMembersBasedOnIds = [];
            $getOrganizationAcceptedManagerMembersBasedOnIds = MemberManagementService::getComponentAcceptedManagerMembersBasedOnIds($organizationId);

            return $getOrganizationAcceptedManagerMembersBasedOnIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function OrganizationChargebeeLimit($organizationData)
    {
        try {
            $getUserCount = ChargebeeHelper::getUserCount($organizationData->id);
            $getManagerCount = ChargebeeHelper::getManagerCount($organizationData->id);
            switch ($organizationData->chargebee_details->plan) {
                case 'free-plan-CAD-Yearly':
                    $plan = 'seed_plan_yearly';
                    $planName = __('responses.seed_plan');
                    break;
                case 'Sprout-Plan-CAD-Yearly':
                    $plan = 'sprout_plan_yearly';
                    $planName = __('responses.sprout_plan');
                    break;
                case 'Budd-Plan-CAD-Yearly':
                    $plan = 'budd_plan_yearly';
                    $planName = __('responses.budd_plan');
                    break;
                case 'Bloom-Plan-CAD-Yearly':
                    $plan = 'bloom_plan_yearly';
                    $planName = __('responses.bloom_plan');
                    break;
                case 'Unlimited-Plan-CAD-Yearly':
                    $plan = 'unlimited_plan';
                    $planName = __('responses.enterprise_plan');
                    break;
            }

            switch ($organizationData->chargebee_details->plan) {
                case 'free-plan-CAD-Monthly':
                    $plan = 'seed_plan_monthly';
                    $planName = __('responses.seed_plan');
                    break;
                case 'Sprout-Plan-CAD-Monthly':
                    $plan = 'sprout_plan_monthly';
                    $planName = __('responses.sprout_plan');
                    break;
                case 'Budd-Plan-CAD-Monthly':
                    $plan = 'budd_plan_monthly';
                    $planName = __('responses.budd_plan');
                    break;
                case 'Bloom-Plan-CAD-Monthly':
                    $plan = 'bloom_plan_monthly';
                    $planName = __('responses.bloom_plan');
                    break;
                case 'Unlimited-Plan-CAD-Monthly':
                    $plan = 'unlimited_plan';
                    $planName = __('responses.enterprise_plan');
                    break;
            }

            if ($organizationData->chargebee_details->plan === 'Unlimited-Plan-CAD-Yearly') {
                $labLimit = 'UnLimited';
                $labProgramLimit = 'UnLimited';
                $preBuildLab = 'UnLimited';
                $challengeLimit = 'UnLimited';
                $challengePathLimit = 'UnLimited';
                $resourceModuleLimit = 'UnLimited';
                $resourceCollectionLimit = 'UnLimited';
                $resourceGroupLimit = 'UnLimited';
                $userInviteLimit = 'UnLimited';
                $managerLimit = 'UnLimited';
            } else {
                $labLimit = $organizationData->chargebee_details->lab_limits;
                $labProgramLimit = $organizationData->chargebee_details->lab_program_limits;
                $preBuildLab = $organizationData->chargebee_details->pre_build_lab_limits;
                $challengeLimit = $organizationData->chargebee_details->challenge_limits;
                $challengePathLimit = $organizationData->chargebee_details->challenge_path_limits;
                $resourceModuleLimit = $organizationData->chargebee_details->resource_module_limits;
                $resourceCollectionLimit = $organizationData->chargebee_details->resource_collection_limits;
                $resourceGroupLimit = $organizationData->chargebee_details->resource_group_limits;
                $userInviteLimit = $organizationData->chargebee_details->user_invite_limits;
                $managerLimit = $organizationData->chargebee_details->organization_invite_limits;
            }

            return [
                'plan'                          => $plan,
                'plan_name'                     => $planName,
                'plan_end_date'                 => UtilityHelper::formatDateTime($organizationData->chargebee_details->trial_end_date),
                'lab_limit'                     => $labLimit,
                'lab_count'                     => $organizationData->labs_count->count(),
                'lab_program_limit'             => $labProgramLimit,
                'lab_program_count'             => $organizationData->lab_programs_count->count(),
                'pre_build_lab_limit'           => $preBuildLab,
                'pre_build_lab_count'           => $organizationData->preBuiltLabs_count->count(),
                'challenge_limit'               => $challengeLimit,
                'challenge_count'               => $organizationData->challenges_count->count(),
                'challenge_path_limit'          => $challengePathLimit,
                'challenge_path_count'          => $organizationData->challenge_paths_count->count(),
                'resource_module_limit'         => $resourceModuleLimit,
                'resource_module_count'         => $organizationData->resource_modules_count->count(),
                'resource_collection_limit'     => $resourceCollectionLimit,
                'resource_collection_count'     => $organizationData->resource_collections_count->count(),
                'resource_group_limit'          => $resourceGroupLimit,
                'resource_group_count'          => $organizationData->resource_groups_count->count(),
                'user_invite_limit'             => $userInviteLimit,
                'user_invite_count'             => $getUserCount->count(),
                'manager_limit'                 => $managerLimit,
                'manager_count'                 => $getManagerCount->count(),
            ];
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function planData($organizationData)
    {
        try {
            $checkLocalEntry = ChargebeeHelper::createChargebeePlanDetails($organizationData->id);
            if ($checkLocalEntry) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return true;
        }
    }

    public function allPlansData()
    {
        try {
            $checkLocalEntry = ChargebeeHelper::getAllPlanDetailsAndLimits();
            if ($checkLocalEntry) {
                return $checkLocalEntry;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return true;
        }
    }

    public function organizationOnboarding($organizationId, $request)
    {
        try {
            DB::beginTransaction();
            $organization = Organization::find($organizationId);
            if ($organization != null) {
                $organization->website = ($request->has('website')) ? $request->website : $organization->website;
                $organization->category = ($request->has('category')) ? $request->category : $organization->category;
                $organization->total_employees = ($request->has('total_employees')) ? $request->total_employees : $organization->total_employees;
                $organization->business_challenge_tacklings = ($request->has('business_challenge_tacklings')) ? $request->business_challenge_tacklings : $organization->business_challenge_tacklings;
                $organization->is_onboarding_completed = '1';
                $organization->save();
                DB::commit();

                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public static function getOrganizationBasedOnCommunityId($communityId)
    {
        try {
            return Organization::where('magnet_community_id', $communityId)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationBasedOnCommunityIds($communityIds)
    {
        try {
            return Organization::whereIn('magnet_community_id', $communityIds)->get();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
