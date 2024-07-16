<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrProjectHelper;

class ProjectBuilder extends BaseBuilder
{
    /**
     * @var array|string[]
     */
    protected array $filterKeys = [
        'project_user_id',
        'project_is_view_enabled',
        'project_privacy',
        'project_stage_id',
        'project_type_id',
        'project_status_id',
        'project_category_id',
    ];

    /**
     * @return SolrBaseHelper|false
     */
    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrHelper SolrProjectHelper */
            $solrHelper = app()->make(SolrProjectHelper::class);

            return $solrHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|ProjectBuilder
     */
    public function whereVerified(): ProjectBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            return $this->where(function ($query) {
                $query->where('user_id', '=', auth()->id())->orWhere('privacy', '=', '0')->orWhere('user_id', '=', auth()->id());
            });
        }

        return $this;
    }
}
