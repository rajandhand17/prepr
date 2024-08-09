<?php

namespace App\Traits\Maestro\Organization;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\OrganizationAddressService;
use App\Services\Maestro\OrganizationMemberService;
use App\Services\Maestro\OrganizationService;
use App\Services\Maestro\OrganizationSocialLinkService;
use Exception;
use Illuminate\Support\Facades\DB;

trait OrganizationTrait
{
    private function createOrganization($request)
    {
        try {
            // Getting org and related tables
            $createdOrg = DB::transaction(function () use ($request) {
                $newOrg = OrganizationService::createOrganization($request);
                $organizationAddress = OrganizationAddressService::createOrganizationAddress($request, $newOrg->id);
                $organizationMember = OrganizationMemberService::createOrganizationMember($request, $newOrg->id);
                $organizationSocialLink = OrganizationSocialLinkService::createOrganizationSocialLink($request, $newOrg->id);
                $selectPlan = OrganizationService::selectPlan($request, $newOrg);

                return [
                    'org'                    => $newOrg,
                    'org_address'            => $organizationAddress,
                    'organizationMember'     => $organizationMember,
                    'organizationSocialLink' => $organizationSocialLink,
                    'selectPlan'             => $selectPlan,
                ];
            });
            // Checking all the tables records inserted successfully
            if ($createdOrg['org'] && $createdOrg['org_address'] && $createdOrg['organizationSocialLink']
                 && $createdOrg['organizationSocialLink'] && $createdOrg['selectPlan']) {
                DB::commit();

                // Returning new created table details
                return $createdOrg['org'];
            }
            DB::rollBack();

            return false;
        } catch(Exception $e) {
            DB::rollback();

            return false;
        }
    }

    private function updateOrganizationById($id, $request)
    {
        try {
            // Getting Lab and related tables
            $updatedOrg = DB::transaction(function () use ($request, $id) {
                $org = OrganizationService::updateOrganizationById($request, $id);
                $orgAddress = OrganizationAddressService::updateOrganizationAddress($request, $id);
                $organizationMember = OrganizationMemberService::updateOrganizationMember($request, $id);
                $organizationSocialLink = OrganizationSocialLinkService::updateOrganizationSocialLink($request, $id);

                //$selectPlan = OrganizationService::selectPlan($request, $org);
                return [
                    'org'                    => $org,
                    'org_address'            => $orgAddress,
                    'organizationSocialLink' => $organizationSocialLink,
                    'organizationMember'     => $organizationMember,
                    // 'selectPlan' => $selectPlan,
                ];
            });

            // Checking all the tables records inserted successfully
            if ($updatedOrg['org'] && $updatedOrg['org_address']
                 && $updatedOrg['organizationSocialLink'] && $updatedOrg['organizationMember']) {
                DB::commit();

                // Returning new created table details
                return $updatedOrg['org'];
            }
            DB::rollBack();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteOrganizationById($id)
    {
        try {
            DB::beginTransaction();
            $deleteOrg = OrganizationService::deleteOrganization($id);
            $deleteLinks = OrganizationSocialLinkService::deleteOrgExternalLinks($id);
            $deleteOrgAddress = OrganizationAddressService::deleteOrganizationAddress($id);
            if ($deleteOrg && $deleteLinks && $deleteOrgAddress == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    private function getOrganizations()
    {
        try {
            $orgs = OrganizationService::getOrganizations();
            if ($orgs) {
                return $orgs;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function selectPlan($organization, $request)
    {
        try {
            $orgs = OrganizationService::selectPlan($organization, $request);
            if ($orgs) {
                return $orgs;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getOrgAssociatedItemsById($org)
    {
        try {
            $associateItems = OrganizationService::getOrgAssociatedItemsById($org);
            if ($associateItems) {
                return $associateItems;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function verifyOrg($request)
    {
        try {
            $message = OrganizationService::verifyOrg($request);
            if ($message) {
                return $message;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getOrganizationById($id)
    {
        try {
            $org = OrganizationService::getOrgById($id);
            if ($org) {
                return $org;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
