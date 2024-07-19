<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrChallengeHelper;

class ChallengeBuilder extends BaseBuilder
{
    /**
     * @var array
     */
    protected array $filterKeys = [
        'challenge_organization_id',
        'challenge_user_id',
        'challenge_is_accessible',
        'challenge_status',
        'challenge_privacy',
        'challenge_category_id',
        'challenge_skills_id',
        'challenge_tags_id',
        'challenge_duration_id',
        'challenge_level_id',
        'challenge_jobs_id',
    ];

    /**
     * @return SolrBaseHelper|false
     */
    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrHelper SolrChallengeHelper */
            $solrHelper = app()->make(SolrChallengeHelper::class);

            return $solrHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|ChallengeBuilder
     */
    public function whereVerified(): ChallengeBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->where('user_id', '=', auth()->id())->orWherehas('organization', function ($query) use ($organizationIds) {
                    $query->where('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
                });
            });
        }

        return $this;
    }
}
