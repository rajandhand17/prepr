<?php

namespace App\Repositories\Api\Manage\LabExternalLink;

use App\Services\Manage\LabExternalLinksService;

class LabExternalLinkRepository implements LabExternalLinkInterface
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
