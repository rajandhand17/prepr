<?php

namespace App\Services;

use App\Events\Project\DeleteProjectAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Project;
use App\Models\ProjectMemberManagement;
use App\Services\ProjectSubmissionRequirementService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ProjectService
{
    public static function getMyProjectIds($userId)
    {
        try {
            $getMyProjects = Project::where('user_id', $userId)->pluck('id');

            return $getMyProjects;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProjectList($getProjectIds, $request)
    {
        try {
            $project_list = Project::select()->whereIn('id', $getProjectIds);

            $project_list = self::filterProjectList($project_list, $request);

            return $project_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            return false;
        }
    }

    public static function filterProjectList($project_list, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $project_list = $project_list->where('projects.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $project_list = $project_list->where('projects.privacy', '0');
                        break;
                    case 'private':
                        $project_list = $project_list->where('projects.privacy', '1');
                        break;
                    default:
                        $project_list = $project_list;
                }
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $project_list = $project_list->orderBy('projects.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $project_list = $project_list->orderBy('projects.title', 'DESC');
                        break;
                    case 'creation_date':
                        $project_list = $project_list->orderBy('projects.created_at', 'ASC');
                        break;
                    default:
                        $project_list = $project_list->orderBy('projects.id', 'ASC');
                }
            }

            return $project_list;
        } catch (Exception $e) {
            return false;
        }
    }

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
            switch ($request->is_view_enabled) {
                case 'yes':
                    $viewEnabled = config('constants.project_view_enabled.yes');
                    break;
                case 'no':
                    $viewEnabled = config('constants.project_view_enabled.no');
                    break;
                default:
                    $viewEnabled = config('constants.project_view_enabled.no');
                    break;
            }

            switch ($request->is_download_enabled) {
                case 'yes':
                    $downloadEnabled = config('constants.project_download_enabled.yes');
                    break;
                case 'no':
                    $downloadEnabled = config('constants.project_download_enabled.no');
                    break;
                default:
                    $downloadEnabled = config('constants.project_download_enabled.no');
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
                    $mediaType = config('constants.project_media_type.image');
                    break;
            }

            switch ($request->privacy) {
                case 'public':
                    $projectPrivacy = config('constants.project_privacy.public');
                    break;
                case 'private':
                    $projectPrivacy = config('constants.project_privacy.private');
                    break;
                default:
                    $projectPrivacy = config('constants.project_privacy.public');
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
            $createProject->is_view_enabled = $viewEnabled;
            $createProject->is_download_enabled = $downloadEnabled;
            $createProject->media_type = $mediaType;
            $createProject->media = $uploadedCoverImage;
            $createProject->privacy = $projectPrivacy;
            $createProject->challenge_id = $challengeId;
            $createProject->lab_id = $labId;
            $createProject->save();

            return $createProject;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectBasedOnSlug($slug)
    {
        try {
            return Project::where('slug', $slug)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $checkProjectName = Project::where('title', $title)->first();
            if ($checkProjectName) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateProject($slug, $request, $uploadedCoverImage)
    {
        try {
            $updateProject = Project::where('slug', $slug)->first();
            if ($updateProject !== null) {
                $viewEnabled = $updateProject->view_enabled;
                switch ($request->is_view_enabled) {
                    case 'yes':
                        $viewEnabled = config('constants.project_view_enabled.yes');
                        break;
                    case 'no':
                        $viewEnabled = config('constants.project_view_enabled.no');
                        break;
                    default:
                        $viewEnabled = config('constants.project_view_enabled.no');
                        break;
                }

                $downloadEnabled = $updateProject->download_enabled;
                switch ($request->is_download_enabled) {
                    case 'yes':
                        $downloadEnabled = config('constants.project_download_enabled.yes');
                        break;
                    case 'no':
                        $downloadEnabled = config('constants.project_download_enabled.no');
                        break;
                    default:
                        $downloadEnabled = config('constants.project_download_enabled.no');
                        break;
                }

                $mediaType = $updateProject->media_type;
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
                        $mediaType = config('constants.project_media_type.image');
                        break;
                }

                $projectPrivacy = $updateProject->privacy;
                switch ($request->privacy) {
                    case 'public':
                        $projectPrivacy = config('constants.project_privacy.public');
                        break;
                    case 'private':
                        $projectPrivacy = config('constants.project_privacy.private');
                        break;
                    default:
                        $projectPrivacy = config('constants.project_privacy.public');
                        break;
                }

                $labId = $updateProject->lab_id;
                if ($request->has('lab_id')) {
                    $checkLab = LabService::getLabBasedOnUUID($request->lab_id);
                    if ($checkLab != null) {
                        $labId = $checkLab->id;
                    }
                }

                $updateProject->language = ($request->has('language')) ? $request->language : $updateProject->language;
                $updateProject->title = ($request->has('title')) ? $request->title : $updateProject->title;
                $updateProject->description = ($request->has('description')) ? $request->description : $updateProject->description;
                $updateProject->is_view_enabled = $viewEnabled;
                $updateProject->is_download_enabled = $downloadEnabled;
                $updateProject->media_type = $mediaType;
                $updateProject->media = $uploadedCoverImage;
                $updateProject->privacy = $projectPrivacy;
                $updateProject->challenge_id = $updateProject->challenge_id;
                $updateProject->lab_id = $labId;
                $updateProject->save();

                return $updateProject;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function projectRequirements($projectData)
    {
        try {
            $challengeData = ChallengeService::getChallengeBasedOnId($projectData->challenge_id);

            $challenge_conditions = [];
            if ($challengeData->challenge_requirements) {
                foreach ($challengeData->challenge_requirements->project_submission_requirement_ids as $project_submission_requirement) {
                    $check_achievement_condition = ProjectSubmissionRequirementService::getProjectSubmissionRequirementByID($challengeData->language, $project_submission_requirement);
                    if ($challengeData->challenge_project_template) {
                        $requirementStatus = '';

                        switch ($check_achievement_condition->id) {
                            case '1':
                                $requirementStatus = ProjectPitchService::checkProjectPitch($projectData->id, $challengeData->challenge_project_template->template_id);
                                break;
                            case '2':
                                $requirementStatus = ProjectPitchService::checkProjectTask($projectData->id, $challengeData->challenge_project_template->template_id);
                                break;
                            case '3':
                                $requirementStatus = ProjectExternalLinksService::checkProjectExternalLink($projectData->id);
                                break;
                            case '4':
                                $requirementStatus = ProjectFileService::checkProjectGallery($projectData->id);
                                break;
                            case '5':
                                $requirementStatus = ProjectFileService::checkProjectFile($projectData->id);
                                break;
                        }
                        $projectStatus = ($requirementStatus) ? 'completed' : 'pending';
                        $projectState = [
                            'status'            => $projectStatus,
                            'Requirement Title' => $check_achievement_condition->title,
                        ];

                        $challenge_conditions[$check_achievement_condition->id] = $projectState;
                    }
                }
            }

            return $challenge_conditions;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteProject($projectId)
    {
        try {
            $project = Project::find($projectId)->delete();
            if ($project) {
                $projectAssociatedData = event(new DeleteProjectAssociatedData($projectId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkProjectRequirementCompleted($projectData)
    {
        try {
            $submitEnabled = true;
            $projectRequirements = self::projectRequirements($projectData);
            if ($projectRequirements !== []) {
                foreach ($projectRequirements as $projectRequirement) {
                    if ($projectRequirement['status'] === 'pending') {
                        $submitEnabled = false;
                    }
                }
            }

            return $submitEnabled;
        } catch (Exception $e) {
            return false;
        }
    }

    public function submitProject($projectData)
    {
        try {
            $projectData->is_submitted = '1';
            $projectData->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAssessProjectIds($getAllChallengeIds, $userData)
    {
        try {
            $getProjectIdBasedOnMember = ProjectMemberManagement::where('email', $userData->email)->pluck('project_id');
            $getOwnProjectIds = self::getMyProjectIds($userData->id);

            $collaborateProjectIds = $getOwnProjectIds->merge($getProjectIdBasedOnMember)->unique();
            $fetchSubmittedProjectIds = Project::whereNotIn('id', $collaborateProjectIds)->whereIn('challenge_id', $getAllChallengeIds)->where('is_submitted', '1')->pluck('id');

            return $fetchSubmittedProjectIds;
        } catch (Exception $e) {
            return false;
        }
    }
}
