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
            $pathsarray=config('s3-upload-path');
            $image_cover = Image::make($request->getRealPath());
            $image_cover->encode('webp', 75);
            $image_contents_cover = $image_cover->__toString();
            $webp_path_cover = $pathsarray[$type].time().'.webp';
            $path_cover=Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
            $path_cover = Storage::disk('s3')->url($webp_path_cover);
            return $path_cover;
        }catch(\Exception $e){
            return $e;
            return false;
        }  
    }

    public static function uploadbase64ImageToS3($request,$type)
    {
        try {   
            $pathsarray=config('s3-upload-path');
               $base64Image=$request;
                // Remove the "data:image/png;base64," prefix from the base64 string
                $imageData = str_replace('data:image/png;base64,', '', $base64Image);
                // Decode the Base64 string
                $imageData = base64_decode($imageData);
                // Generate a unique file name
                $webp_path_cover = $pathsarray[$type].time().'.webp';
                $path_cover=Storage::disk('s3')->put($webp_path_cover, $imageData);
                $path_cover = Storage::disk('s3')->url($webp_path_cover);
                return $path_cover;
                // // Generate a unique file name
                // $fileName = uniqid() . '.png';
                // // Define the file path 
                // $filePath = public_path() . $fileName;
                // // Save the image to the local system
                // $storedimage= file_put_contents($filePath, $imageData);
                // return $filePath;
        } catch (\Exception $e){
            return $e;
           return false;
        }
    }
}