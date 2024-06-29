<?php

namespace App\Helpers;

class LanguageColumnHelper
{
    public static function getLanguageColumnName($language, $column_name)
    {
        try {
            $final_column_name = $column_name;
            if ($language != 'en') {
                if ($language == trim($language) && strpos($language, ' ') !== false) {
                    $language = str_replace(' ', '_', $language);
                }
                if ($language == trim($language) && strpos($language, '-') !== false) {
                    $language = str_replace('-', '_', $language);
                }
                $final_column_name = $language.'_'.$column_name;
            }

            return $final_column_name;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
