<?php
namespace App\Helpers;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManagerStatic as Image;


class FileDeleteHelper{
  
    public static function deleteImageFromS3($request)
    {
      return Storage::disk('s3')->delete($request);
    }
}