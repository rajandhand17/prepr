<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrResourceModuleHelper;

class ResourceBuilder extends BaseBuilder
{
    protected array $filterKeys = [
        'resource_module_is_from_go1',
        'resource_module_status',
        'resource_module_user_id',
        'resource_module_org_id',
        'resource_module_is_global',
        'resource_module_skills_id',
        'resource_module_tags_id',
        'resource_module_duration_id',
        'resource_module_level_id',
        'resource_module_privacy',
    ];

    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrHelper SolrResourceModuleHelper */
            $solrHelper = app()->make(SolrResourceModuleHelper::class);

            return $solrHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|ResourceBuilder
     */
    public function whereVerified(): ResourceBuilder
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
