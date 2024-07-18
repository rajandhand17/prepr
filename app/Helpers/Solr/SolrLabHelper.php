<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\Lab;

class SolrLabHelper extends SolrBaseHelper
{
    /**
     * @var string[]
     */
    protected array $searchQueryFields = ['title'];

    /**
     * @tutorial SOLR COLLECTION NAME
     *
     * @var string
     */
    protected string $solrCollection = SolrCollection::LABS;

    /**
     * @var string
     */
    protected string $modelClass = Lab::class;

    /**
     * @var string
     */
    protected string $schemaName = 'lab';

    /**
     * @param $value
     *
     * @return array
     */
    public function formatData($value): array
    {
        return [
            'id'              => data_get($value, 'id'),
            'language'        => data_get($value, 'language'),
            'title'           => data_get($value, 'title'),
            'slug'            => data_get($value, 'slug'),
            'status'          => data_get($value, 'status'),
            'lab_category_id' => data_get($value, 'category_id'),
            'verification'    => data_get($value, 'is_verified'),
            'description'     => data_get($value, 'description'),
            'privacy'         => data_get($value, 'privacy'),
            'address'         => data_get($value->address, 'address'),
            'city'            => data_get($value->address, 'city'),
            'country'         => data_get($value->address, 'country'),
            'organization_id' => data_get($value->organization, 'id'),
            'skills_id'       => collect($value->skills ?? [])->pluck('foreign_id')->toArray(),
            'tags_id'         => collect($value->tags ?? [])->pluck('foreign_id')->toArray(),
            'skill_groups_id' => collect($value->skill_groups ?? [])->pluck('foreign_id')->toArray(),
            'user_id'         => data_get($value, 'user_id'),
            'duration_id'     => [data_get($value, 'duration_id')],
            'level_id'        => [data_get($value, 'level_id')],
            'type_id'         => [data_get($value, 'type')],
        ];
    }
}
