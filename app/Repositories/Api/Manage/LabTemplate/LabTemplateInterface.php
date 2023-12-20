<?php

namespace App\Repositories\Api\Manage\LabTemplate;

interface LabTemplateInterface
{
    public function createLabTemplate($slug, $lab);
}
