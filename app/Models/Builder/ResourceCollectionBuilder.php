<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrResourceCollectionHelper;

class ResourceCollectionBuilder extends BaseBuilder
{
    /**
     * @var array|string[]
     */
    protected array $filterKeys = [
        'resource_collection_organisation_id',
        'resource_collection_user_id',
        'resource_collection_status',
        'resource_collection_is_accessible',
        'resource_collection_skills_id',
        'resource_collection_duration_id',
        'resource_collection_level_id',
        'resource_collection_privacy',
    ];

    /**
     * @return SolrBaseHelper|false
     */
    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrHelper SolrResourceCollectionHelper */
            $solrHelper = app()->make(SolrResourceCollectionHelper::class);

            return $solrHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|ResourceCollectionBuilder
     */
    public function whereVerified(): ResourceCollectionBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->where('user_id', '=', auth()->id())->orWherehas('getOrganization', function ($query) use ($organizationIds) {
                    $query->where('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
                });
            });
        }

        return $this;
    }
}
