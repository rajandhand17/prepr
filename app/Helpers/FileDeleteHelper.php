<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileDeleteHelper
{
    public static function deleteImageFromS3($request)
    {
        try {
            if (Storage::disk('s3')->exists($request)) {
                $response = Storage::disk('s3')->delete($request);
            }

            return $response;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $e;
        }
    }
}
