<?php
namespace App\Helpers;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManagerStatic as Image;


class FileDeleteHelper{
  
    public static function deleteImageFromS3($request)
    {
      if(Storage::disk('s3')->exists($request)) {
        $response=Storage::disk('s3')->delete($request);
      }
      return $response;
    }
}