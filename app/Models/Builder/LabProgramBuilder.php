<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrLabHelper;
use App\Helpers\Solr\SolrLabProgramHelper;

class LabProgramBuilder extends BaseBuilder
{
    /**
     * @var array
     */
    protected array $filterKeys = [
        'lab_program_organization_id',
        'lab_program_status',
        'lab_program_published',
        'lab_program_privacy',
        'lab_program_category_id',
        'lab_program_skills_id',
        'lab_program_skill_groups_id',
        'lab_program_skill_stacks_id',
        'lab_program_tags_id',
        'lab_program_level_id',
        'lab_program_duration_id',
    ];

    /**
     * @return SolrBaseHelper|false
     */
    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrLabHelper SolrLabHelper */
            $solrLabHelper = app()->make(SolrLabProgramHelper::class);

            return $solrLabHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|LabProgramBuilder
     */
    public function whereVerified(): LabProgramBuilder
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
