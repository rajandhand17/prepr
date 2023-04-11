<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\FileUploadHelper;

class MemberManagementCsvUpload extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table="member_management_csv_upload";
    protected $fillable=[
        "inviter_id","csv","is_processed","process_status","total_count","failure_count","processed_csv"
    ];
    
    public function uploadCsv($component,$slug,$request)
    {    
        // $file = $request->file('csv_file');
        // if($file!==null){
        //     $profile_images_path=FileUploadHelper::uploadImageToS3($request->profile_image,"member_mangement");
        //     if($profile_images_path==false){
        //         $response= ['success' => false, 'message' => __('responses.fail_organization_image_upload')];
        //         return $response;   
        //     }
        // }
        
    }
}
