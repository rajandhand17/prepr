<?php

namespace App\Services\Maestro;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Jobs\Chargebee\SubscribePlanJob;
use App\Models\Category;
use App\Models\Organization;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;

class OrganizationService
{
    public static function getOrgById($id){
        try {
            $organization = Organization::find($id);
            if ($organization) {
                return $organization;
            }
            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
    public static function updateOrganizationById($request, $id)
    {
        try {
            $organization = Organization::findOrFail($id);
            if (!empty($organization)) {
                $input = $request->all();
                $profile_image = '';
                $cover_image = '';
                if ($request->file('profile_image')) {
                    $profile_image = $request->file('profile_image')->store('uploads/organization', 's3');
                    $organization->profile_image = $profile_image ? $profile_image : 'NULL';
                    $organization->save();
                }
                $input = $request->except('cover_image', 'people_name', 'user_name', 'user_role', 'org_social', 'social_url');
                if ($request->file('cover_image')) {
                    $cover_image = $request->file('cover_image')->store('uploads/organization', 's3');
                    $organization->cover_image = $cover_image ? $cover_image : 'NULL';
                    $organization->save();
                }
                $organization->user_id = $request->user_id;
                $organization->title = $request->title;
                $organization->website = $request->website;
                $organization->about = $request->about;
                $organization->category = $request->category;
                $organization->status = $request->status;
                $org = $organization->save();

                return $org;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteOrganization($id)
    {
        try {
            $organization = Organization::find($id);
            if ($organization) {
                return $organization->delete();
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function createOrganization($request)
    {
        try {
            $profile_image = '';
            $cover_image = '';
            if ($request->file('profile_image')) {
                $profile_image = $request->file('profile_image')->store('uploads/organization', 's3');
            }

            if ($request->file('cover_image')) {
                $cover_image = $request->file('cover_image')->store('uploads/organization', 's3');
            }
            $model = new Organization();
            //$vanityUpdatedSlug = $this->removeHttp($request->vanity_link);
            $vanityUpdatedSlug = UtilityHelper::generateSlug($request->title, $model);
            $data = [
                'uuid' => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                // 'user_id' => $request->user_id,
                'user_id'       => $request->user_id,
                'title'         => $request->title,
                'language'      => $request->language,
                'slug'          => $vanityUpdatedSlug,
                'vanity_slug'   => $vanityUpdatedSlug,
                'cover_image'   => $cover_image ? $cover_image : null,
                'profile_image' => $profile_image ? $profile_image : null,
                'website'       => $request->website,
                'about'         => $request->about,
                'category'      => $request->category,
                'status'        => $request->status,
                'vanity_link'   => $request->vanity_link,
            ];
            $organization = Organization::create($data);

            return $organization;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getOrganizations()
    {
        try {
            return Organization::orderBy('id', 'desc');
        } catch (Exception $e) {
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

    public static function selectPlan($request, $organization)
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

    public static function getOrganization($id)
    {
        try {
            $organization = Organization::select('title', 'id');
            if (!empty($id)) {
                $organization = $organization->where(['id' => $id]);
            }

            return $organization->pluck('title', 'id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getOrgAssociatedItemsById($org)
    {
        try {
            $user = !empty($org->user_id) ? User::where(['id' => $org->user_id])->pluck('username', 'id') : null;
            $category = !empty($org->category) ? Category::where(['id' => $org->category])->pluck('title', 'id') : null;

            return ['user' => $user, 'category' => $category];
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getOrganizationById($organization_id)
    {
        try {
            $organization = Organization::where(['id' => $organization_id])->pluck('title', 'id');
            if ($organization != null) {
                return $organization;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getOrganizationsById($request)
    {
        try {
            $orgList = Organization::where(['language' => $request->language, 'status' => '1'])->orderBy('id', 'DESC')->take(30);

            if ($request->search) {
                $orgList->where('title', 'LIKE', '%'.$request->search.'%');
            }

            $orgList = $orgList->pluck('title', 'id');
            $count = 0;
            $orgResponse = $finalResult = [];
            foreach ($orgList as $key => $orgObj) {
                $orgResponse[$count]['id'] = $key;
                $orgResponse[$count]['text'] = $orgObj;
                $count++;
            }
            $finalResult['result'] = $orgResponse;

            return response()->json($finalResult);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
                return false;
            }
        }

    public static function verifyOrg($request)
    {
        try {
            if ($request->org_v == '0') {
                Organization::where('id', $request->org_id)->update(['is_verified' => '1']);
                $message = 'Organization has been successfully verified.';
            } else {
                Organization::where('id', $request->org_id)->update(['is_verified' => '0']);
                $message = 'Organization verification has been removed.';
            }
            if ($message) {
                return $message;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

}
