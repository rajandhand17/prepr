<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationExternalLink;
use Exception;
use Illuminate\Support\Facades\DB;

class OrganizationExternalLinkService
{
    public function createOrganizationExternalLinks($request, $organizationId)
    {
        try {
            DB::beginTransaction();
            if ($request->has('external_links') && $request->get('external_link_ids')) {
                if (count($request->external_link_ids) > 0) {
                    foreach ($request->external_link_ids as $key => $value) {
                        if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                            $organizationExternalLink = new OrganizationExternalLink();
                            $organizationExternalLink->organization_id = $organizationId;
                            $organizationExternalLink->social_media_link = $request->external_links[$key];
                            $organizationExternalLink->social_link_id = $value;
                            $organizationExternalLink->save();
                        }
                    }
                }
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function updateOrganizationExternalLinks($request, $organizationId)
    {
        try {
            DB::beginTransaction();
            if ($request->has('external_links') && $request->get('external_links') && $request->has('external_link_ids') && $request->get('external_link_ids')) {
                $getOrganizationlinks = OrganizationExternalLink::where('organization_id', $organizationId)->delete();
                foreach ($request->external_link_ids as $key => $externalLinkId) {
                    if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                        $organizationExternalLink = new OrganizationExternalLink();
                        $organizationExternalLink->organization_id = $organizationId;
                        $organizationExternalLink->social_media_link = $request->external_links[$key];
                        $organizationExternalLink->social_link_id = $externalLinkId;
                        $organizationExternalLink->save();
                    }
                }
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }
}
