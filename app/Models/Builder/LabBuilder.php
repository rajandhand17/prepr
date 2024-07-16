<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Helpers\Solr\SolrLabHelper;

class LabBuilder extends BaseBuilder
{
    /**
     * @var array
     */
    protected array $filterKeys = [
        'status',
        'lab_category_id',
        'verification',
        'privacy',
        'organization_id',
        'skills_id',
        'tags_id',
        'skill_groups_id',
        'duration_id',
        'level_id',
        'type_id',
    ];

    /**
     * @return SolrBaseHelper|false
     */
    public function getSolrInstance(): SolrBaseHelper|false
    {
        try {
            return app()->make(SolrLabHelper::class);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return $this|LabBuilder
     */
    public function whereVerified(): LabBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->where('user_id', '=', auth()->id())->orWhereHas('organization', function ($query) use ($organizationIds) {
                    $query->where('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
                });
            });
        }

        return $this;
    }
}
