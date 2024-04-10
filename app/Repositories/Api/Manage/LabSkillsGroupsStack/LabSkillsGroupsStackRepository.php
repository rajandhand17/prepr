<?php

namespace App\Repositories\Api\Manage\LabSkillsGroupsStack;

use App\Services\Manage\LabExternalLinksService;

class LabSkillsGroupsStackRepository implements LabSkillsGroupsStackInterface
{
    protected $labExternalLinksService;

    public function __construct(LabExternalLinksService $labExternalLinksService)
    {
        $this->labExternalLinksService = $labExternalLinksService;
    }

    public function store($request, $lab)
    {
        $addExternalLinks = $this->labExternalLinksService->store($request, $lab);
    }
}
