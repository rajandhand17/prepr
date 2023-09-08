<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
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

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $organization_list = $organization_list->whereIn('organizations.category', $request->category);
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

            return $organization_list;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getOrganizationBasedOnSlug($slug)
    {
        try {
            return Organization::where('slug', $slug)->first();
        } catch (\Exception $e) {
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
            $organization->website = isset($request->website) ? $request->website : null;
            $organization->about = isset($request->about) ? $request->about : null;
            $organization->category = $request->category;
            $organization->status = ($request->status == 'draft') ? '0' : (($request->status == 'publish') ? '1' : '3');
            $organization->total_employees = $request->total_employees;
            $organization->save();
            auth()->user()->attachRole('organization_owner', $organization);

            DB::commit();

            return $organization;
        } catch (\Exception $e) {
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
            DB::rollBack();

            return false;
        }
    }

    public static function deleteOrganization($slug = null, $language = 'en')
    {
        try {
            Organization::where('slug', $slug)->delete();

            return true;
        } catch (\Exception $e) {
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
            dd($e);
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
}
