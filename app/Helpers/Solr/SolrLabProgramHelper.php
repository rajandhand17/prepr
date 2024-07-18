<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\LabProgram;

class SolrLabProgramHelper extends SolrBaseHelper
{
    /**
     * @var string[]
     */
    protected array $searchQueryFields = ['lab_program_title'];

    /**
     * @tutorial SOLR COLLECTION NAME
     *
     * @var string
     */
    protected string $solrCollection = SolrCollection::LAB_PROGRAMS;

    /**
     * @var string
     */
    protected string $modelClass = LabProgram::class;

    /**
     * @var string
     */
    protected string $schemaName = 'lab_program';

    public function formatData($value): array
    {
        return [
            'id'                          => data_get($value, 'id'),
            'lab_program_title'           => data_get($value, 'title'),
            'lab_program_language'        => data_get($value, 'language'),
            'lab_program_organization_id' => data_get($value, 'organization_id'),
            'lab_program_user_id'         => data_get($value, 'user_id'),
            'lab_program_status'          => data_get($value, 'status'),
            'lab_program_published'       => data_get($value, 'published'),
            'lab_program_is_accessible'   => data_get($value, 'is_accessible'),
            'lab_program_privacy'         => data_get($value, 'privacy'),
            'lab_program_description'     => data_get($value, 'description'),
            'lab_program_category_id'     => data_get($value, 'category_id'),
            'lab_program_skills_id'       => collect($value->skills ?? [])->pluck('foreign_id')->toArray(),
            'lab_program_skill_groups_id' => collect($value->skill_groups ?? [])->pluck('foreign_id')->toArray(),
            'lab_program_skill_stacks_id' => collect($value->skill_stacks ?? [])->pluck('foreign_id')->toArray(),
            'lab_program_tags_id'         => collect($value->tags ?? [])->pluck('foreign_id')->toArray(),
            'lab_program_duration_id'     => data_get($value, 'duration_id'),
            'lab_program_level_id'        => data_get($value, 'level_id'),
        ];
    }
}
