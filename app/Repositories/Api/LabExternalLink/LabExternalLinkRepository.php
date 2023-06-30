<?php

namespace App\Repositories\Api\LabExternalLink;

use App\Services\LabExternalLinksService;

class LabExternalLinkRepository implements LabExternalLinkInterface
{   
    protected $labExternalLinksService;
    public function __construct(LabExternalLinksService $labExternalLinksService)
    {
        $this->labExternalLinksService=$labExternalLinksService;
    }
    public function store($request,$lab){
        $addExternalLinks=$this->labExternalLinksService->store($request,$lab);
    }
}