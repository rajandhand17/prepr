<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\LabMarketplace;

class SolrLabMarketPlaceHelper extends SolrBaseHelper
{
    /**
     * @var string[]
     */
    protected array $searchQueryFields = ['lab_marketplace_title'];

    /**
     * @tutorial SOLR COLLECTION NAME
     *
     * @var string
     */
    protected string $solrCollection = SolrCollection::LAB_MARKETPLACE;

    /**
     * @var string
     */
    protected string $modelClass = LabMarketplace::class;

    /**
     * @var string
     */
    protected string $schemaName = 'lab_marketplace';

    public function formatData($value): array
    {
        $skillsIds = $value->skills()->pluck('foreign_id');

        return [
            'id'                              => data_get($value, 'id'),
            'lab_marketplace_title'           => data_get($value, 'title'),
            'lab_marketplace_slug'            => data_get($value, 'slug'),
            'lab_marketplace_language'        => data_get($value, 'language'),
            'lab_marketplace_organization_id' => data_get($value, 'organization_id'),
            'lab_marketplace_user_id'         => data_get($value, 'user_id'),
            'lab_marketplace_status'          => data_get($value, 'status'),
            'lab_marketplace_is_verified'     => data_get($value, 'is_verified'),
            'lab_marketplace_privacy'         => data_get($value, 'privacy'),
            'lab_marketplace_description'     => data_get($value, 'description'),
            'lab_marketplace_skills_id'       => $skillsIds,
            'lab_marketplace_category_id'     => data_get($value, 'category_id'),
            'lab_template_duration_id'        => [data_get($value, 'duration_id')],
            'lab_template_level_id'           => [data_get($value, 'level_id')],
        ];
    }
}
