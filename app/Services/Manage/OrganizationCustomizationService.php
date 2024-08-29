<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\OrganizationCustomization;
use Exception;
use Illuminate\Support\Facades\DB;

class OrganizationCustomizationService
{
    public function updateOrganizationCustomLoginRegistration($request, $organizationData)
    {
        try {
            DB::beginTransaction();
            if ($request->enable_custom_login_and_registration == 'none') {
                OrganizationCustomization::where('organization_id', $organizationData->id)->delete();

                return true;
            }

            $checkExisitingCustomDetails = OrganizationCustomization::where('organization_id', $organizationData->id)->first();
            if ($request->enable_custom_login_and_registration !== 'none') {
                if ($checkExisitingCustomDetails) {
                    $organizationCustomization = $checkExisitingCustomDetails;
                } else {
                    $organizationCustomization = new OrganizationCustomization();
                }
                $customLogoImage = ($checkExisitingCustomDetails != null) ? str_replace(config('site-settings.aws_url'), '', $checkExisitingCustomDetails->custom_logo_image) : null;
                if ($request->has('custom_logo_image') && $request->use_main_org_logo == 'yes') {
                    $customLogoImage = FileUploadHelper::uploadImageToS3($request->custom_logo_image, 'organization');
                }

                $customHeroImage = ($checkExisitingCustomDetails != null) ? str_replace(config('site-settings.aws_url'), '', $checkExisitingCustomDetails->custom_hero_image) : null;
                if ($request->has('custom_hero_image')) {
                    $customHeroImage = FileUploadHelper::uploadImageToS3($request->custom_hero_image, 'organization');
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
                $enableCustomLoginRegistration = $request->enable_custom_login_and_registration == 'yes' ? '1' : '0';
                $organizationCustomization->organization_id = $organizationData->id;
                $organizationCustomization->enable_custom_login_and_registration = $enableCustomLoginRegistration;
                $organizationCustomization->use_main_org_logo = $useMainOrgLogo;
                $organizationCustomization->custom_logo_image = $customLogoImage;
                $organizationCustomization->custom_hero_image = $customHeroImage;
                $organizationCustomization->custom_background_color = $request->has('custom_background_color') ? $request->custom_background_color : $checkExisitingCustomDetails->custom_background_color;
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
