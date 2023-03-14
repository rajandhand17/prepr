<?php
namespace App\Helpers;
use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploadHelper {
     
    public function uploadImageToS3($request)
    {
            $image_cover = Image::make($request->image->getRealPath());
            $image_cover->encode('webp', 75);
            $image_contents_cover = $image_cover->__toString();
            $webp_path_cover = 'organizations/cover_images/'.time().'.webp';
            $path_cover=Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
            $path_cover = Storage::disk('s3')->url($webp_path_cover);
            return $path_cover;
    }

}