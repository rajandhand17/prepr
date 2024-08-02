<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationSocialLink;
use App\Models\SocialLink;
use Exception;

class OrganizationSocialLinkService
{
    public static function createOrganizationSocialLink($request, $orgId)
    {
        try {
            if (!empty(array_filter($request->social_url))) {
                foreach ($request->social_url as $key => $value) {
                    // $org_social_data['user_id'] = $request->user_id;
                    $org_social_data['organization_id'] = $orgId;
                    $org_social_data['social_link_id'] = $request->org_social[$key];
                    $org_social_data['social_media_link'] = $value;
                    OrganizationSocialLink::create($org_social_data);
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationSocialLink($id)
    {
        try {
            $orgSocialLink = OrganizationSocialLink::where('organization_id', $id)->get();
            if ($orgSocialLink->isNotEmpty()) {
                foreach ($orgSocialLink as $value) {
                    $social_name = SocialLink::where('id', $value->social_link_id)->first();
                    $value->link_name = (!empty($social_name->title)) ? $social_name->title : '';
                }
            }

            return $orgSocialLink;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateOrganizationSocialLink($request, $id)
    {
        try {
            OrganizationSocialLink::where('organization_id', $id)->forceDelete();
            if (!empty(array_filter($request->social_url))) {
                foreach ($request->social_url as $key => $value) {
                    $org_social_data['organization_id'] = $id;
                    $org_social_data['social_link_id'] = $request->org_social[$key];
                    $org_social_data['social_media_link'] = $value;
                    OrganizationSocialLink::create($org_social_data);
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteOrgExternalLinks($id)
    {
        try {
            if (OrganizationSocialLink::where('organization_id', $id)->delete()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
