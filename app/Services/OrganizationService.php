<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Organization;
use DB;

use function auth;

class OrganizationService
{
    public static function checkOrganizationExist($request)
    {
        try {
            $organization_exists = Organization::select('id')->where('title', $request->title)->withTrashed()->first();
            if ($organization_exists == null) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function checkOrganizationExistInTrash($request)
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

    public static function getOrganizationExistBasedOnSlug($slug)
    {
        try {
            $organization = Organization::where('slug', $slug)->first();
            if ($organization != null) {
                return $organization;
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
            if ($profile_image_path == false) {
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
            if ($cover_image_path == false) {
                return false;
            }

            return $cover_image_path;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateOrganizationProfileImage($request)
    {
        try {
            $profile_image_path = FileUploadHelper::uploadbase64ImageToS3($request->profile_image, 'organization');
            if ($profile_image_path == false) {
                return false;
            }

            return $profile_image_path;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateOrganizationCoverImage($request)
    {
        try {
            $profile_image_path = FileUploadHelper::uploadbase64ImageToS3($request->cover_image, 'organization');
            if ($profile_image_path == false) {
                return false;
            }

            return $profile_image_path;
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

    public static function createOrganization($request, $profile_image_path, $cover_image_path)
    {
        try {
            DB::beginTransaction();
            $model = new Organization();

            $organization = new Organization();
            $organization->language = isset($request->language) ? $request->language : 'en';
            $organization->user_id = auth()->user()->id;
            $organization->title = $request->title;
            $organization->display_name = $request->title;
            $organization->description = isset($request->description) ? $request->description : null;
            $organization->slug = UtilityHelper::generateSlug($request->slug, $model);
            $organization->cover_image = $cover_image_path;
            $organization->profile_image = $profile_image_path;
            $organization->website = isset($request->website) ? $request->website : null;
            $organization->about = isset($request->about) ? $request->about : null;
            $organization->category = $request->category;
            if ($request->status !== null) {
                $organization->status = $request->status;
            }
            $organization->total_employees = $request->total_employees;
            if ($organization->save()) {
                DB::commit();

                return $organization;
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public static function view($search = null, $language = 'en')
    {
        try {
            $organization_list = Organization::with('categoryDetail')->with('organizationAddress')->with('organizationMembers');
            if ($search != null) {
                $organization_list = $organization_list->where('slug', $search);
            }
            $organization_list = $organization_list->get();
            if (!$organization_list->isEmpty()) {
                $organization_list->transform(function ($item) {
                    if ($item['status'] == 0) {
                        $item['status'] = 'draft';
                    }
                    if ($item['status'] == 1) {
                        $item['status'] = 'published';
                    }
                    if ($item['status'] == 2) {
                        $item['status'] = 'deactivated';
                    }

                    return $item;
                });

                return $organization_list;
            }

            return 'not_exists';
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function list($language)
    {
        try {
            $organization_list = Organization::select('id', 'language', 'title', 'slug', 'description', 'cover_image', 'profile_image', 'website', 'about', 'category', 'status', 'is_verified', 'total_employees');
            $organization_list = $organization_list->get();
            if (!$organization_list->isEmpty()) {
                return $organization_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function delete($slug = null, $language = 'en')
    {
        try {
            $exists = Organization::select('id')->where('slug', $slug)->first();
            if ($exists !== null) {
                $organization = Organization::where('slug', $slug)->delete();
                if ($organization) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return 'not_exists';
            }
        } catch (\Exception $e) {
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
                $organization->status = ($request->has('status')) ? $request->status : $organization->status;
                $organization->total_employees = ($request->has('total_employees')) ? $request->total_employees : $organization->total_employees;
                $organization->save();

                if ($organization) {
                    DB::commit();

                    return $organization;
                }
                DB::rollBack();

                return false;
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
