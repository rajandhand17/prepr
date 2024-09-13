<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class CryptHelper
{
    /**
     * @param $value
     *
     * @return false|string
     */
    public static function encrypt($value): false|string
    {
        try {
            return Crypt::encrypt($value);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param string|null $encoded
     *
     * @return false|mixed
     */
    public static function decrypt(string|null $encoded): mixed
    {
        try {
            if (!$encoded) {
                return false;
            }

            return Crypt::decrypt($encoded);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
