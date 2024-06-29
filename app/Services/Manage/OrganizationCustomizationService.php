<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\OrganizationCustomization;
use Exception;
use Illuminate\Support\Facades\DB;

class OrganizationCustomizationService
{
    public function createOrganizationCustomLoginRegistration($request, $organizationData)
    {
        try {
            DB::beginTransaction();
            if ($request->enable_custom_login_and_registration == 'yes') {
                $customLogoImage = !empty($request->custom_logo_image) ? FileUploadHelper::uploadImageToS3($request->custom_logo_image, 'organization') : null;
                $customHeroImage = !empty($request->custom_hero_image) ? FileUploadHelper::uploadImageToS3($request->custom_hero_image, 'organization') : null;

                switch ($request->use_main_org_logo) {
                    case 'no':
                        $useMainOrgLogo = config('constants.use_main_org_logo.no');
                        break;
                    case 'yes':
                        $useMainOrgLogo = config('constants.use_main_org_logo.yes');
                        $customLogoImage = $organizationData->getRawOriginal('profile_image');
                        break;
                    default:
                        $useMainOrgLogo = config('constants.use_main_org_logo.no');
                        break;
                }

                $organizationCustomization = new OrganizationCustomization();
                $organizationCustomization->organization_id = $organizationData->id;
                $organizationCustomization->enable_custom_login_and_registration = '1';
                $organizationCustomization->use_main_org_logo = $useMainOrgLogo;
                $organizationCustomization->custom_login_url = $request->custom_login_url;
                $organizationCustomization->custom_logo_image = $customLogoImage;
                $organizationCustomization->custom_hero_image = $customHeroImage;
                $organizationCustomization->custom_background_color = $request->custom_background_color ?? null;
                $organizationCustomization->save();
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function updateOrganizationCustomLoginRegistration($request, $organizationData)
    {
        try {
            DB::beginTransaction();
            $deleteExisitingCustomDetails = OrganizationCustomization::where('organization_id', $organizationData->id)->delete();
            if ($request->enable_custom_login_and_registration == 'yes') {
                if ($request->old_custom_logo_image) {
                    $customLogoImage = str_replace(config('site-settings.aws_url'), '', $request->old_custom_logo_image);
                } else {
                    $customLogoImage = !empty($request->custom_logo_image) ? FileUploadHelper::uploadImageToS3($request->custom_logo_image, 'organization') : null;
                }

                if ($request->old_custom_hero_image) {
                    $customHeroImage = str_replace(config('site-settings.aws_url'), '', $request->old_custom_hero_image);
                } else {
                    $customHeroImage = !empty($request->custom_hero_image) ? FileUploadHelper::uploadImageToS3($request->custom_hero_image, 'organization') : null;
                }

                switch ($request->use_main_org_logo) {
                    case 'no':
                        $useMainOrgLogo = config('constants.use_main_org_logo.no');
                        break;
                    case 'yes':
                        $useMainOrgLogo = config('constants.use_main_org_logo.yes');
                        $customLogoImage = ($customLogoImage != null) ? $customLogoImage : $organizationData->getRawOriginal('profile_image');
                        break;
                    default:
                        $useMainOrgLogo = config('constants.use_main_org_logo.no');
                        break;
                }

                $organizationCustomization = new OrganizationCustomization();
                $organizationCustomization->organization_id = $organizationData->id;
                $organizationCustomization->enable_custom_login_and_registration = '1';
                $organizationCustomization->use_main_org_logo = $useMainOrgLogo;
                $organizationCustomization->custom_login_url = $request->custom_login_url;
                $organizationCustomization->custom_logo_image = $customLogoImage;
                $organizationCustomization->custom_hero_image = $customHeroImage;
                $organizationCustomization->custom_background_color = $request->custom_background_color ?? null;
                $organizationCustomization->save();
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public static function organizationCustomization($organizationId)
    {
        try {
            $organizationCustomizationCheck = OrganizationCustomization::where('organization_id', $organizationId)->first();
            if ($organizationCustomizationCheck) {
                $organizationCustomizationCheck = OrganizationCustomization::where('organization_id', $organizationId)->delete();
                if (!$organizationCustomizationCheck) {
                    return false;
                }

                return true;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
