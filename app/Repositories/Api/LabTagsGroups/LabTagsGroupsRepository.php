<?php

namespace App\Repositories\Api\LabTagsGroups;

use App\Services\LabTagsGroupsService;


class LabSkillsGroupsStackRepository implements LabTagsGroupsInterface
{   
    protected $labTagsGroupsService;
    public function __construct(LabTagsGroupsService $labTagsGroupsService)
    {
        $this->labTagsGroupsService=$labTagsGroupsService;
    }
    public function store($request,$lab){
        try {
            $labTagsGroupsService=$this->labTagsGroupsService->store($request,$lab);
            return $labTagsGroupsService;
        } catch (\Exception $e) {
            return $e;
        }
    }
}