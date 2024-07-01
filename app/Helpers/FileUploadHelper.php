<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploadHelper
{
    public static function uploadImageToS3($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $image_cover = Image::make($request->getRealPath());
            $image_cover->encode('webp', 75);
            $image_contents_cover = $image_cover->__toString();
            $webp_path_cover = $pathsarray[$type].time().'.webp';
            Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);

            return $webp_path_cover;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadVideoToS3($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $videoData = $request->store($pathsarray[$type], 's3');

            return $videoData;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadDocToS3($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $fileData = $request->store($pathsarray[$type], 's3');

            return $fileData;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadbase64ImageToS3($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $base64Image = $request;
            // Remove the "data:image/png;base64," prefix from the base64 string
            $imageData = str_replace('data:image/png;base64,', '', $base64Image);
            // Decode the Base64 string
            $imageData = base64_decode($imageData);
            // Generate a unique file name
            $webp_path_cover = $pathsarray[$type].time().'.webp';
            $path_cover = Storage::disk('s3')->put($webp_path_cover, $imageData);
            $path_cover = Storage::disk('s3')->url($webp_path_cover);

            return $path_cover;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fileUpload($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $file = $request->file('cover_image');
            $image_contents_cover = fopen($file->getRealPath(), 'rb');
            $webp_path_cover = $pathsarray[$type].time().'.webp';
            Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);

            return $webp_path_cover;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadLocalStorageImageToS3($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $image_cover = Image::make($request->getFile()->getRealPath());
            $image_cover->encode('webp', 75);
            $image_contents_cover = $image_cover->__toString();
            $fileOriginalName = $request->getFile()->getFileName();
            $webp_path_cover = $pathsarray[$type].$fileOriginalName;
            Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);

            return $webp_path_cover;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadLocalStoragePDFToS3($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $filePath = $request->getFile()->getRealPath();
            $fileOriginalName = $request->getFile()->getFileName();
            $webp_path_cover = $pathsarray[$type].$fileOriginalName;
            Storage::disk('s3')->put($webp_path_cover, file_get_contents($filePath));

            return $webp_path_cover;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function UploadVideoDocToS3($request, $type)
    {
        try {
            $pathsarray = config('s3-upload-path');
            $videoData = $request->store($pathsarray[$type], 's3');

            return $videoData;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
