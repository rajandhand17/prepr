<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrChallengeTemplateHelper;

class ChallengeTemplateBuilder extends BaseBuilder
{
    protected array $filterKeys = [
        'challenge_template_user_id',
        'challenge_template_organization_id',
        'challenge_template_category_id',
        'challenge_template_skills_id',
        'challenge_template_status',
        'challenge_template_open_status',
        'challenge_template_tags_id',
        'challenge_template_privacy',
        'challenge_template_level_id',
        'challenge_template_duration_id',
    ];

    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrHelper SolrChallengeTemplateHelper */
            $solrHelper = app()->make(SolrChallengeTemplateHelper::class);

            return $solrHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|ChallengeTemplateBuilder
     */
    public function whereVerified(): ChallengeTemplateBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->wherehas('organization', function ($query) use ($organizationIds) {
                    $query->where('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
                });
            });
        }

        return $this;
    }
}
