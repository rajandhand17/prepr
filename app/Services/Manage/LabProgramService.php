<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabProgram;

class LabProgramService
{
    public function getLabProgramList($request)
    {
        $getLabProgramList = LabProgram::select();

        return $getLabProgramList->paginate(config('site-settings.pagination_per_page'));
    }

    public static function getLabProgramBasedOnSlug($slug)
    {
        try {
            return LabProgram::where('slug', $slug)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createLabProgram($request, $upload_media)
    {
        try {
            $privacy = config('constants.lab_privacy.no');
            switch($request->privacy) {
                case 'yes':
                    $privacy = config('constants.lab_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.lab_privacy.no');
                    break;
                default:
                    $privacy = config('constants.lab_privacy.yes');
                    break;
            }

            $status = config('constants.lab_status.draft');
            switch($request->status) {
                case 'draft':
                    $status = config('constants.lab_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.lab_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.lab_status.archive');
                    break;
                default:
                    $status = config('constants.lab_status.draft');
                    break;
            }
            $model = new LabProgram();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $labIdJson = json_encode($request->lab_id);
            $labProgram = new LabProgram();
            $labProgram->language = $request->language;
            $labProgram->title = $request->title;
            $labProgram->slug = $slug;
            $labProgram->description = $request->description;
            $labProgram->lab_id = $labIdJson;
            $labProgram->organization_id  = $request->organization_id;
            $labProgram->category_id  = $request->category_id;
            $labProgram->duration_id = $request->duration_id;
            $labProgram->level_id  = $request->level_id;
            $labProgram->user_id = auth()->user()->id;
            $labProgram->media = $upload_media;
            $labProgram->privacy = $privacy;
            $labProgram->status = $status;
            $labProgram->is_auto_created = '0';
            $labProgram->prize = $request->prize;
            $labProgram->points = $request->points;
            $labProgram->trophy = $request->trophy;
            $labProgram->save();
            return $labProgram;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function uploadLabProgramMedia($image)
    {
        try {
            $upload_lab_cover_image = FileUploadHelper::uploadImageToS3($image, 'lab_program');
            if ($upload_lab_cover_image == false) {
                return false;
            }

            return $upload_lab_cover_image;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function checkSlug($slug){
        try {
            return LabProgram::where('slug',$slug)->first();
        }catch (\Exception $e){
            return false;
        }
    }

    public function delete($slug){
        try{
            return LabProgram::where('slug',$slug)->delete();
        }catch (\Exception $e){
            return false;
        }
    }

    public function checkNameExistsOrNot($title){
        try{
            $checkLabProgramName = LabProgram::where('title', $title)->first();
            if ($checkLabProgramName) {
                return true;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }
}
