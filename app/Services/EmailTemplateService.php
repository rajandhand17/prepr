<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
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
            switch ($module_type) {
                case 0:
                    $getTemplate = $getTemplate->where('module_type', config('constants.email_template_module_type.organization'));
                    break;
            }

            return $getTemplate->first();
        } catch (\Exception $e) {
            return false;
        }
    }
}
