<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\ChallengePath;

class SolrChallengePathHelper extends SolrBaseHelper
{
    protected array $searchQueryFields = ['challenge_path_title'];

    protected string $solrCollection = SolrCollection::CHALLENGE_PATHS;

    protected string $modelClass = ChallengePath::class;

    protected string $schemaName = 'challenge_path';

    public function formatData($value): array
    {
        $skillsIds = $value->skills()->pluck('foreign_id');

        return [
            'id'                             => data_get($value, 'id'),
            'challenge_path_title'           => data_get($value, 'title'),
            'challenge_path_language'        => data_get($value, 'language'),
            'challenge_path_organization_id' => data_get($value, 'organization_id'),
            'challenge_path_user_id'         => data_get($value, 'user_id'),
            'challenge_path_status'          => data_get($value, 'status'),
            'challenge_path_privacy'         => data_get($value, 'privacy'),
            'challenge_path_description'     => data_get($value, 'description'),
            'challenge_path_skills_id'       => $skillsIds,
            'challenge_path_duration_id'     => [data_get($value, 'duration')],
            'challenge_path_level_id'        => [data_get($value, 'level_id')],
            'challenge_path_is_accessible'   => data_get($value, 'is_accessible'),
            'challenge_path_category_id'     => data_get($value, 'category_id'),
        ];
    }
}
