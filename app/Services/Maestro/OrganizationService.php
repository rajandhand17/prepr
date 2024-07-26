<?php

namespace App\Services\Maestro;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Jobs\Chargebee\SubscribePlanJob;
use App\Models\Organization;
use App\Models\OrganizationAddress;
use App\Models\OrganizationMember;
use App\Models\OrganizationSocialLink;
use Exception;
use HiFolks\RandoPhp\Randomize;

class OrganizationService
{
    public static function updateOrganizationById($id, $request)
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
                // $organization->latitude = $request->latitude;
                // $organization->longitude = $request->longitude;
                // $organization->address = $request->address;
                $organization->status = $request->status;
                // $organization->vanity_link = $request->vanity_link;
                //$organization->slug = $request->slug;
                $organization->save();

                if ($request->address) {
                    OrganizationAddress::where('organization_id', $organization->id)->delete();
                    $organization_address = new OrganizationAddress();
                    $organization_address->organization_id = $organization->id;
                    $organization_address->full_address = $request->address;
                    $organization_address->save();
                }
                $people = OrganizationMember::where('organization_id', $organization->id)->forceDelete();

                if (!empty(array_filter($request->people_name))) {
                    foreach ($request->people_name as $key => $value) {
                        $people = new OrganizationMember();
                        $image = '';
                        $aws = env('AWS_URL');
                        if (isset($request->file('image')[$key])) {
                            if ($request->file('image')[$key]) {
                                $file = $request->file('image')[$key];
                                $image = $file->store('uploads/people', 's3');
                                $image = str_replace($aws, '', $image);
                                $people->image = $image;
                                $people->organization_id = $organization->id;
                                $people->name = $value;
                                $people->description = $request->people_des[$key];
                                $people->save();
                            }
                        } else {
                            $people->name = $value;
                            $people->description = $request->people_des[$key];
                            $people->organization_id = $organization->id;
                            $image = $request->image[$key];
                            $image = str_replace($aws, '', $image);
                            $people->image = $image;
                            $people->save();
                        }
                    }
                }
                OrganizationSocialLink::where('organization_id', $id)->forceDelete();
                if (!empty(array_filter($request->social_url))) {
                    foreach ($request->social_url as $key => $value) {
                        $org_social_data['organization_id'] = $organization->id;
                        $org_social_data['social_link_id'] = $request->org_social[$key];
                        $org_social_data['social_media_link'] = $value;
                        OrganizationSocialLink::create($org_social_data);
                    }
                }

                return true;
            }

            return false;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function deleteOrganization($id)
    {
        try {
            $organization = Organization::find($id);
            $people = OrganizationMember::where('organization_id', $id)->delete();

            if ($organization) {
                return $organization->delete();
            }

            return false;
        } catch (Exception $e) {
            dd($e);

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
            $org_address = [
                'organization_id' => $organization->id,
                'latitude'        => $request->latitude,
                'longitude'       => $request->longitude,
                'full_address'    => $request->address,
                'city'            => $request->city2,

            ];

            OrganizationAddress::create($org_address);

            if (!empty(array_filter($request->people_name))) {
                foreach ($request->people_name as $key => $value) {
                    $image = '';
                    if (isset($request->image[$key])) {
                        $image = $request->image[$key]->store('uploads/people', 's3');
                    } else {
                        $image = '';
                    }

                    $people_data = [
                        'organization_id' => $organization->id,
                        'name'            => $value,
                        'description'     => $request->people_des[$key],
                        'image'           => $image,
                    ];
                    $people = OrganizationMember::create($people_data);
                }
            }
            if (!empty(array_filter($request->social_url))) {
                foreach ($request->social_url as $key => $value) {
                    // $org_social_data['user_id'] = $request->user_id;
                    $org_social_data['organization_id'] = $organization->id;
                    $org_social_data['social_link_id'] = $request->org_social[$key];
                    $org_social_data['social_media_link'] = $value;
                    OrganizationSocialLink::create($org_social_data);
                }
            }

            $profile_image = $organization->profile_image;
            $selectPlan = self::selectPlan($organization, $request);

            return true;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function getOrganizations()
    {
        try {
            return Organization::orderBy('id', 'desc');
        } catch (Exception $e) {
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

    public static function selectPlan($organization, $request)
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
            return false;
        }
    }
}
