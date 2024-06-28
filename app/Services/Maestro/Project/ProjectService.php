<?php

namespace App\Services\Maestro\Project;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Lab;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectIndustry;
use App\Models\ProjectStage;
use App\Models\ProjectStatus;
use App\Models\ProjectType;
use App\Models\ProjectVertical;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ProjectService
{
    public static function createProject($request)
    {
        try {
            $model = new Project();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $createProject = new Project();
            $createProject->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $createProject->language = 'en';
            $createProject->user_id = (int) $request->user_id;
            $createProject->title = $request->title;
            $createProject->slug = $slug;
            $createProject->description = $request->description;
            $createProject->challenge_id = (int) $request->challenge_id;
            $createProject->lab_id = (int) $request->lab_id;
            $createProject->category_id = $request->category;
            $createProject->type_id = $request->type;
            $createProject->industry_id = $request->industry;
            $createProject->stage_id = $request->stage;
            $createProject->vertical_id = $request->verticals;
            $createProject->status_id = $request->status;
            $createProject->privacy = $request->privacy;
            if ($createProject->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProject($id)
    {
        try {
            $project = Project::find($id);
            if (!empty($project)) {
                return $project->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectById($id)
    {
        try {
            $project = Project::findOrFail($id);
            if ($project != null) {
                return $project;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateProjectById($id, $request)
    {
        try {
            $updateProject = Project::findOrFail($id);
            if (!empty($updateProject)) {
                $updateProject->user_id = (int) $request->user_id;
                $updateProject->title = $request->title;
                $updateProject->description = $request->description;
                $updateProject->challenge_id = (int) $request->challenge_id;
                $updateProject->lab_id = (int) $request->lab_id;
                $updateProject->category_id = $request->category;
                $updateProject->type_id = $request->type;
                $updateProject->industry_id = $request->industry;
                $updateProject->stage_id = $request->stage;
                $updateProject->vertical_id = $request->verticals;
                $updateProject->status_id = $request->status;
                $updateProject->privacy = $request->privacy;
                if ($updateProject->save()) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectAssociateItems($type)
    {
        try {
            switch ($type) {
                case 'user':
                    $responseData = User::pluck('username', 'id')->prepend('Please Select', '');
                    break;
                case 'lab':
                    $responseData = Lab::pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'challenge':
                    $responseData = Challenge::pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'stage':
                    $responseData = ProjectStage::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'category':
                    $responseData = Category::Where('components', 'like', '%project%')->pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'type':
                    $responseData = ProjectType::pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'status':
                    $responseData = ProjectStatus::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'industry':
                    $responseData = ProjectIndustry::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'vertical':
                    $responseData = ProjectVertical::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
                    break;
                case 'privacy':
                    $responseData = ['0' => 'Public', '1' => 'Private'];
                    break;
                case 'team':
                    $responseData = User::pluck('username', 'email');
                    break;
            }

            return $responseData;
        } catch (Exception $e) {
            return false;
        }
    }

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
            return false;
        }
    }
}
