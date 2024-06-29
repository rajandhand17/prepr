<?php

namespace App\Repositories\Api\Manage\LabTagsGroups;

use App\Helpers\UtilityHelper;
use App\Services\Manage\LabTagsGroupsService;

class LabTagsGroupsRepository implements LabTagsGroupsInterface
{
    protected $labTagsGroupsService;

    public function __construct(LabTagsGroupsService $labTagsGroupsService)
    {
        $this->labTagsGroupsService = $labTagsGroupsService;
    }

    public function store($request, $lab)
    {
        try {
            $labTagsGroupsService = $this->labTagsGroupsService->store($request, $lab);

            return $labTagsGroupsService;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $e;
        }
    }
}
