<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ProjectFile;
use Exception;

class ProjectFileService
{
    public function addProjectFile($projectId, $request)
    {
        try {
            if (isset($request->file_upload) && !empty($request->file_upload)) {
                foreach ($request->file_upload as $file_upload) {
                    if (false !== mb_strpos($file_upload->getMimeType(), 'image')) {
                        $file_type = config('constants.project_file_type.image');
                        $uploaded_file_path = FileUploadHelper::uploadImageToS3($file_upload, 'project_file');
                    } elseif (false !== mb_strpos($file_upload->getMimeType(), 'video')) {
                        $file_type = config('constants.project_file_type.video');
                        $uploaded_file_path = FileUploadHelper::uploadVideoToS3($file_upload, 'project_file');
                    } elseif (false !== mb_strpos($file_upload->getMimeType(), 'audio')) {
                        $file_type = config('constants.project_file_type.audio');
                        $uploaded_file_path = FileUploadHelper::uploadDocToS3($file_upload, 'project_file');
                    } else {
                        $file_type = config('constants.project_file_type.docs');
                        $uploaded_file_path = FileUploadHelper::uploadDocToS3($file_upload, 'project_file');
                    }

                    if ($uploaded_file_path == false) {
                        return false;
                    }

                    $storeData = self::uploadData($projectId, $uploaded_file_path, $file_type, $file_upload);
                    if ($storeData) {
                        $activity = auth()->user()->full_name.' '.__('responses.project_media_activty').' '.$file_upload->getClientOriginalName();
                        ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);
                    }
                    if (!$storeData) {
                        return false;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function uploadData($projectId, $uploadedFile, $file_type, $file_upload)
    {
        try {
            $projectData = new ProjectFile();
            $projectData->project_id = $projectId;
            $projectData->title = $file_upload->getClientOriginalName();
            $projectData->path = $uploadedFile;
            $projectData->type = $file_type;
            $projectData->save();

            return $projectData;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function checkProjectGallery($projectId)
    {
        try {
            $checkProjectGallery = ProjectFile::where('project_id', $projectId)->whereNotIn('type', ['docs', 'audio'])->count();
            $projectGallery = false;
            if ($checkProjectGallery > 0) {
                $projectGallery = true;
            }

            return $projectGallery;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function checkProjectFile($projectId)
    {
        try {
            $checkProjectFile = ProjectFile::where('project_id', $projectId)->whereNotIn('type', ['audio', 'video'])->count();
            $projectFile = false;
            if ($checkProjectFile > 0) {
                $projectFile = true;
            }

            return $projectFile;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteProjectFile($projectId)
    {
        try {
            $checkProjectFileExists = ProjectFile::where('project_id', $projectId)->pluck('id');
            if ($checkProjectFileExists->isNotEmpty()) {
                $deleteProjectFile = ProjectFile::whereIn('id', $checkProjectFileExists)->delete();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function deleteProjectMediaFile($request, $projectId)
    {
        try {
            $getProjectFile = ProjectFile::where(['id' => $request->media_id, 'project_id' => $projectId, 'type' => $request->type]);
            $mediaName = $getProjectFile->first()->title;
            if ($getProjectFile->delete()) {
                $activity = auth()->user()->full_name.' '.__('responses.project_media_removed_activty').' '.$mediaName;
                ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
