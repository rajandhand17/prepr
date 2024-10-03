<?php

namespace App\Traits\Maestro\AutoCreateTemplate;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\AutoCreateTemplates\AutoCreateTemplatesService;

trait AutoCreateTemplateTrait
{
    public function createUpdateAutoTemplate($request)
    {
        try {
            return AutoCreateTemplatesService::createUpdateAutoTemplate($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
