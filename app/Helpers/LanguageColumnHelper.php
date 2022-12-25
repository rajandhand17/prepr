<?php
namespace App\Helpers;

use Exception;

class LanguageColumnHelper{


    public static function getLanguageColumnName($language,$column_name){
        try{
            if ($language == trim($language) && strpos($language, ' ') !== false) {
                $language = str_replace(' ', '_', $language);
            }
            if ($language == trim($language) && strpos($language, '-') !== false) {
                $language = str_replace('-', '_', $language);
            }
            return $language.'_'.$column_name;
        }
        catch (\Exception $e){
            return false;
        }
    }

}
