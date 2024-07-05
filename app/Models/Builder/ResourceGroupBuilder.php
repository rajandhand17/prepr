<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrResourceGroupHelper;

class ResourceGroupBuilder extends BaseBuilder
{
    /**
     * @var array|string[]
     */
    protected array $filterKeys = [
        'resource_group_organisation_id',
        'resource_group_user_id',
        'resource_group_status',
        'resource_group_privacy',
        'resource_group_skills_id',
        'resource_group_duration_id',
        'resource_group_level_id',
        'resource_group_is_accessible',
    ];

    /**
     * @return SolrBaseHelper|false
     */
    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrHelper SolrResourceGroupHelper */
            $solrHelper = app()->make(SolrResourceGroupHelper::class);

            return $solrHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|ResourceGroupBuilder
     */
    public function whereVerified(): ResourceGroupBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->wherehas('getOrganization', function ($query) use ($organizationIds) {
                    $query->where('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
                });
            });
        }

        return $this;
    }
}
