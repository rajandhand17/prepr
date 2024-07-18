<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\ChallengeTemplate;

class SolrChallengeTemplateHelper extends SolrBaseHelper
{
    protected array $searchQueryFields = ['challenge_template_title'];

    protected string $solrCollection = SolrCollection::CHALLENGE_TEMPLATES;

    protected string $modelClass = ChallengeTemplate::class;

    protected string $schemaName = 'challenge_template';

    public function formatData($value): array
    {
        $skillsIds = $value->skills()->pluck('foreign_id');
//        $tagsIds = $value->tags()->pluck('foreign_id');

        return [
            'id'                                 => data_get($value, 'id'),
            'challenge_template_title'           => data_get($value, 'title'),
            'challenge_template_language'        => data_get($value, 'language'),
            'challenge_template_slug'            => data_get($value, 'slug'),
            'challenge_template_user_id'         => data_get($value, 'user_id'),
            'challenge_template_organization_id' => data_get($value, 'organization_id'),
            'challenge_template_description'     => data_get($value, 'description'),
            'challenge_template_category_id'     => data_get($value, 'category_id'),
            'challenge_template_skills_id'       => $skillsIds,
            'challenge_template_status'          => data_get($value, 'status'),
            'challenge_template_open_status'     => data_get($value, 'is_open'),
            'challenge_template_tags_id'         => [],
            'challenge_template_privacy'         => data_get($value, 'privacy'),
            'challenge_template_level_id'        => data_get($value, 'level_id'),
            'challenge_template_duration_id'     => data_get($value, 'duration_id'),
        ];
    }
}
