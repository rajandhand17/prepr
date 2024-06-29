<?php

namespace App\Services\Manage;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\EmailTemplate;

class EmailTemplateService
{
    public static function getEmailTemplate($template_type, $module_type, $language)
    {
        try {
            $subject_column_name = LanguageColumnHelper::getLanguageColumnName($language, 'subject');
            $body_content_column_name = LanguageColumnHelper::getLanguageColumnName($language, 'body_content');

            $getTemplate = EmailTemplate::select("$subject_column_name as subject", "$body_content_column_name as body_content");
            switch ($template_type) {
                case 0:
                    $getTemplate = $getTemplate->where('template_type', config('constants.email_template_type.invitation'));
                    break;
            }

            return $getTemplate->where('module_type', $module_type)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
