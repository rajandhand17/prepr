<?php
namespace App\Helpers;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploadHelper {
     
    public static function uploadImageToS3($request,$type)
    {           
        try {
            $pathsarray=config('S3Path');
            $image_cover = Image::make($request->getRealPath());
            $image_cover->encode('webp', 75);
            $image_contents_cover = $image_cover->__toString();
            $webp_path_cover = $pathsarray[$type].time().'.webp';
            $path_cover=Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
            $path_cover = Storage::disk('s3')->url($webp_path_cover);
            return $path_cover;
        }catch(\Exception $e){
            return false;
        }  
    }

}