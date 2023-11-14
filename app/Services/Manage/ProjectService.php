<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Project;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ProjectService
{
    public static function uploadCoverImage($coverImage)
    {
        try {
            $upload_project_cover_image = FileUploadHelper::uploadImageToS3($coverImage, 'project');
            if ($upload_project_cover_image == false) {
                return false;
            }

            return $upload_project_cover_image;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createProject($request, $uploadedCoverImage)
    {
        try {
            switch ($request->view_enabled) {
                case 'yes':
                    $viewEnabled = config('constants.project_view_enabled.yes');
                    break;
                case 'no':
                    $viewEnabled = config('constants.project_view_enabled.no');
                    break;
                default:
                    $viewEnabled = config('constants.project_view_enabled.yes');
                    break;
            }

            switch ($request->download_enabled) {
                case 'yes':
                    $downloadEnabled = config('constants.project_download_enabled.yes');
                    break;
                case 'no':
                    $downloadEnabled = config('constants.project_download_enabled.no');
                    break;
                default:
                    $downloadEnabled = config('constants.project_download_enabled.yes');
                    break;
            }

            switch ($request->media_type) {
                case 'image':
                    $mediaType = config('constants.project_media_type.image');
                    break;
                case 'embedded':
                    $mediaType = config('constants.project_media_type.embedded');
                    break;
                case 'video':
                    $mediaType = config('constants.project_media_type.video');
                    break;
                default:
                    $mediaType = config('constants.project_media_type.yes');
                    break;
            }

            switch ($request->status) {
                case 'public':
                    $projectStatus = config('constants.project_status.public');
                    break;
                case 'private':
                    $projectStatus = config('constants.project_status.private');
                    break;
                default:
                    $projectStatus = config('constants.project_status.public');
                    break;
            }

            $labId = null;
            if ($request->has('lab_id')) {
                $labId = LabService::getLabBasedOnUUID($request->lab_id)->id;
            }
            $challengeId = ChallengeService::getChallengeBasedOnUUID($request->challenge_id)->id;

            $model = new Project();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $createProject = new Project();
            $createProject->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $createProject->language = $request->language;
            $createProject->user_id = auth()->user()->id;
            $createProject->title = $request->title;
            $createProject->slug = $slug;
            $createProject->description = $request->description;
            $createProject->view_enabled = $viewEnabled;
            $createProject->download_enabled = $downloadEnabled;
            $createProject->media_type = $mediaType;
            $createProject->media = $uploadedCoverImage;
            $createProject->status = $projectStatus;
            $createProject->challenge_id = $challengeId;
            $createProject->lab_id = $labId;
            $createProject->save();

            return $createProject;
        } catch (Exception $e) {
            return false;
        }
    }
}
