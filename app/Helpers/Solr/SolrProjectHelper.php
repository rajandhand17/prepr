<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\Project;

class SolrProjectHelper extends SolrBaseHelper
{
    /**
     * @var string[]
     */
    protected array $searchQueryFields = ['project_title'];

    /**
     * @tutorial SOLR COLLECTION NAME
     *
     * @var string
     */
    protected string $solrCollection = SolrCollection::PROJECTS;

    /**
     * @var string
     */
    protected string $modelClass = Project::class;

    /**
     * @var string
     */
    protected string $schemaName = 'project';

    public function formatData($value): array
    {
        return [
            'id'                      => data_get($value, 'id'),
            'project_title'           => data_get($value, 'title'),
            'project_slug'            => data_get($value, 'slug'),
            'project_language'        => data_get($value, 'language'),
            'project_user_id'         => data_get($value, 'user_id'),
            'project_is_view_enabled' => data_get($value, 'is_view_enabled'),
            'project_privacy'         => data_get($value, 'privacy'),
            'project_description'     => data_get($value, 'description'),
            'project_stage_id'        => data_get($value, 'stage_id'),
            'project_type_id'         => data_get($value, 'type_id'),
            'project_status_id'       => data_get($value, 'status_id'),
            'project_category_id'     => data_get($value, 'category_id'),
        ];
    }
}
