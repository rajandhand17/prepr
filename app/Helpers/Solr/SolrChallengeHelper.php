<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\Challenge;

class SolrChallengeHelper extends SolrBaseHelper
{
    protected array $searchQueryFields = ['challenge_title'];

    protected string $solrCollection = SolrCollection::CHALLENGES;

    protected string $modelClass = Challenge::class;

    protected string $schemaName = 'challenge';

    public function formatData($value): array
    {
        $skillsIds = $value->skills()->pluck('foreign_id');
        // $tagsIds = $value->tags()->pluck('foreign_id');
        $jobsIds = $value->jobs()->pluck('job_title_id');

        return [
            'id'                        => data_get($value, 'id'),
            'challenge_title'           => data_get($value, 'title'),
            'challenge_language'        => data_get($value, 'language'),
            'challenge_slug'            => data_get($value, 'slug'),
            'challenge_organization_id' => data_get($value->organization, 'id'),
            'challenge_user_id'         => data_get($value, 'user_id'),
            'challenge_is_accessible'   => data_get($value, 'is_accessible'),
            'challenge_status'          => data_get($value, 'status'),
            'challenge_privacy'         => data_get($value, 'privacy'),
            'challenge_description'     => data_get($value, 'description'),
            'challenge_category'        => data_get($value->getCategory, 'name'),
            'challenge_category_id'     => data_get($value, 'category_id'),
            'challenge_skills_id'       => $skillsIds ?: [],
            'challenge_tags_id'         => [],
            'challenge_duration_id'     => [data_get($value, 'duration_id')],
            'challenge_level_id'        => [data_get($value, 'level_id')],
            'challenge_jobs_id'         => $jobsIds,
        ];
    }
}
