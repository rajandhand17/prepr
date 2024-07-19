<?php

namespace App\Helpers\Maestro;

use Exception;

class UtilityHelper
{
    public static function getColumName($iso, $fieldName)
    {
        try {
            if ($iso == 'en') {
                $columName = $fieldName;
            } else {
                $columName = $iso;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName.'_'.$fieldName;
            }

            return $columName;
        } catch (Exception $e) {
            return $fieldName;
        }
    }
}
