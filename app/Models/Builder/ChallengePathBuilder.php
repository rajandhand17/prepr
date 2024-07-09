<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrChallengePathHelper;

class ChallengePathBuilder extends BaseBuilder
{
    /**
     * @var array
     */
    protected array $filterKeys = [
        'challenge_path_organization_id',
        'challenge_path_user_id',
        'challenge_path_status',
        'challenge_path_privacy',
        'challenge_path_skills_id',
        'challenge_path_duration_id',
        'challenge_path_level_id',
        'challenge_path_is_accessible',
        'challenge_path_category_id',
    ];

    /**
     * @return SolrBaseHelper|false
     */
    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            /** @var $solrHelper SolrChallengePathHelper */
            $solrHelper = app()->make(SolrChallengePathHelper::class);

            return $solrHelper;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|ChallengePathBuilder
     */
    public function whereVerified(): ChallengePathBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->wherehas('getOrganization', function ($query) use ($organizationIds) {
                    $query->where('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
                });
            });
        }

        return $this;
    }
}
