<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrLabMarketPlaceHelper;

class LabMarketPlaceBuilder extends BaseBuilder
{
    protected array $filterKeys = [
        'lab_marketplace_status',
        'lab_marketplace_duration_id',
        'lab_marketplace_level_id',
        'lab_marketplace_organization_id',
        'lab_marketplace_skills_id',
        'lab_marketplace_category_id',
        'lab_marketplace_privacy',
    ];

    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            return app()->make(SolrLabMarketPlaceHelper::class);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|LabMarketPlaceBuilder
     */
    public function whereVerified(): LabMarketPlaceBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->where('user_id', '=', auth()->id())->orWherehas('organization', function ($query) use ($organizationIds) {
                    $query->where('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
                });
            });
        }

        return $this;
    }
}
