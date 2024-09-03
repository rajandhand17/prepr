<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\ChallengePitch;
use App\Models\ChallengeTask;
use App\Models\Lab;
use App\Models\PitchTemplate;
use App\Models\Project as ModelsProject;
use App\Models\ProjectAdditionalInfo;
use App\Models\ProjectExternalLink;
use App\Models\ProjectFile;
use App\Models\ProjectIndustry;
use App\Models\ProjectMemberManagement;
use App\Models\ProjectPitchValue;
use App\Models\ProjectSkill;
use App\Models\ProjectSocialActivity;
use App\Models\ProjectStage;
use App\Models\ProjectStatus;
use App\Models\ProjectTaskValue;
use App\Models\ProjectTemplate;
use App\Models\ProjectType;
use App\Models\ProjectVertical;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Project extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old Projects table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for Projects table started.');
            DB::beginTransaction();

            // Fetch Projects from Legacy Database in chucks of 1000 data
            DB::connection('mysql2')->table('projects')->chunkById(1000, function ($projects) {
                foreach ($projects as $key => $project) {
                    // Fetch User based on user_id
                    $checkUser = User::find($project->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $fetchChallenge = Challenge::find($project->challenge_id);
                    $fetchLab = Lab::find($project->lab_id);

                    switch ($project->privacy) {
                        case 'public':
                            $projectPrivacy = '0';
                            break;
                        case 'private':
                            $projectPrivacy = '1';
                            break;
                        default:
                            $projectPrivacy = '0';
                            break;
                    }

                    switch ($project->file_download_privacy) {
                        case 'public':
                            $projectDownloadPrivacy = '0';
                            break;
                        case 'private':
                            $projectDownloadPrivacy = '1';
                            break;
                        default:
                            $projectDownloadPrivacy = '0';
                            break;
                    }

                    switch ($project->recruiting_status) {
                        case '0':
                            $projectRecruitingStatus = '0';
                            break;
                        case '1':
                            $projectRecruitingStatus = '1';
                            break;
                        default:
                            $projectRecruitingStatus = '0';
                            break;
                    }

                    $mediaType = 'image';
                    switch ($project->mediaType) {
                        case 'image':
                            $mediaType = '0';
                            break;
                        case 'embedded':
                            $mediaType = '1';
                            break;
                        case 'video':
                            $mediaType = '2';
                            break;
                        default:
                            $mediaType = '0';
                            break;
                    }

                    // For main Project table
                    $checkProject = ModelsProject::find($project->id);
                    if ($checkProject) {
                        $newProject = $checkProject;
                    } else {
                        $newProject = new ModelsProject();
                    }

                    $submissionType = '0';
                    $submissionDescription = null;
                    $checkSubmission = DB::connection('mysql2')->table('challange_projects')->where(['project_id' => $project->id])->whereNull('deleted_at')->first();
                    if ($checkSubmission) {
                        if ($checkSubmission->late_submission_reason != null) {
                            $submissionType = '2';
                            $submissionDescription = $checkSubmission->late_submission_reason;
                        } else {
                            switch ($checkSubmission->status) {
                                case '0':
                                    $submissionType = '0';
                                    break;

                                case '1':
                                    $submissionType = '1';
                                    break;
                            }
                        }
                    }

                    $createdAt = $project->created_at != null ? Carbon::createFromTimestamp($project->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                    $updatedAt = $project->updated_at != null ? Carbon::createFromTimestamp($project->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                    $deletedAt = $project->deleted_at != null ? Carbon::createFromTimestamp($project->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                    // For main project table
                    $newProject->id = $project->id;
                    $newProject->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newProject->language = $project->language;
                    $newProject->user_id = $project->user_id;
                    $newProject->title = $project->title;
                    $newProject->slug = $project->slug;
                    $newProject->description = $project->description;
                    $newProject->is_view_enabled = $projectPrivacy;
                    $newProject->is_download_enabled = $projectDownloadPrivacy;
                    $newProject->media_type = $mediaType;
                    $newProject->media = $project->image ?? null;
                    $newProject->privacy = $projectPrivacy;
                    $newProject->recruiting_status = $projectRecruitingStatus;
                    $newProject->challenge_id = $fetchChallenge != null ? $fetchChallenge->id : null;
                    $newProject->lab_id = $fetchLab != null ? $fetchLab->id : null;
                    $newProject->total_share = $project->total_share;
                    $newProject->is_submitted = $submissionType;
                    $newProject->late_submission_reason = $submissionDescription;
                    $newProject->created_at = $createdAt;
                    $newProject->updated_at = $updatedAt;
                    $newProject->deleted_at = $deletedAt;
                    $newProject->save();

                    // For project additional info table
                    $checkProjectAdditionalInfo = ProjectAdditionalInfo::where('project_id', $project->id)->first();
                    if ($checkProjectAdditionalInfo) {
                        $newProjectAdditionalInfo = $checkProjectAdditionalInfo;
                    } else {
                        $newProjectAdditionalInfo = new ProjectAdditionalInfo();
                    }

                    $category = null;
                    if ($project->category != null) {
                        $checkProjectCategory = Category::find($project->category);
                        if ($checkProjectCategory) {
                            $category = $checkProjectCategory->id;
                        }
                    }

                    $industry = null;
                    if ($project->industry != null) {
                        $checkProjectIndustry = ProjectIndustry::find($project->industry);
                        if ($checkProjectIndustry) {
                            $industry = $checkProjectIndustry->id;
                        }
                    }

                    $vertical = null;
                    if ($project->verticals != null) {
                        $checkProjectVertical = ProjectVertical::find($project->verticals);
                        if ($checkProjectVertical) {
                            $vertical = $checkProjectVertical->id;
                        }
                    }

                    $type = null;
                    if ($project->type != null) {
                        $checkProjectType = ProjectType::find($project->type);
                        if ($checkProjectType) {
                            $type = $checkProjectType->id;
                        }
                    }

                    $stage = null;
                    if ($project->stage != null) {
                        $checkProjectStage = ProjectStage::find($project->stage);
                        if ($checkProjectStage) {
                            $stage = $checkProjectStage->id;
                        }
                    }

                    $status = null;
                    if ($project->status != null) {
                        $checkProjectStatus = ProjectStatus::find($project->status);
                        if ($checkProjectStatus) {
                            $status = $checkProjectStatus->id;
                        }
                    }

                    $newProjectAdditionalInfo->project_id = $project->id;
                    $newProjectAdditionalInfo->category_id = $category;
                    $newProjectAdditionalInfo->industry_id = $industry;
                    $newProjectAdditionalInfo->verticals_id = $vertical;
                    $newProjectAdditionalInfo->type_id = $type;
                    $newProjectAdditionalInfo->stage_id = $stage;
                    $newProjectAdditionalInfo->status_id = $status;
                    $newProjectAdditionalInfo->save();

                    // For Project skills table
                    $arraySkills = json_decode($project->skills, true);
                    if (!empty($arraySkills)) {
                        ProjectSkill::where('project_id', $project->id)->delete();
                        foreach (array_filter($arraySkills) as $skill) {
                            $checkProjectSkill = Skill::find($skill);
                            if ($checkProjectSkill) {
                                $projectSkill = new ProjectSkill();
                                $projectSkill->project_id = $project->id;
                                $projectSkill->skill_id = $skill;
                                $projectSkill->save();
                            }
                        }
                    }

                    // For project member and team leader
                    $teamId = '';
                    $merged = $teamIdArray1 = $teamIdArray2 = [];
                    if (!empty($project->team)) {
                        $teamId = $project->team;
                        if (!empty($teamId)) {
                            $merged = explode(',', $teamId);
                        }
                    }

                    $userId = DB::connection('mysql2')->table('invite_data')->where(['module' => 'project', 'module_id' => $project->id])->pluck('user_id');
                    if (!empty($userId->toArray())) {
                        $teamIdArray2 = $userId->toArray();
                        if (!empty($merged)) {
                            $merged = array_unique(array_merge($merged, $teamIdArray2));
                        } elseif (!empty($teamIdArray2) && empty($merged)) {
                            $merged = $teamIdArray2;
                        }
                    }

                    $projectDataMembers = User::whereIn('id', $merged)->get();

                    $selected_member = [];
                    if ($projectDataMembers->isNotEmpty()) {
                        $membersDetails = DB::connection('mysql2')->table('project_members')->where('project_id', $project->id)->get();
                        $memberIndex = 0;
                        foreach (json_decode($projectDataMembers) as $members) {
                            foreach (json_decode($membersDetails) as $detail) {
                                if ($members->email == $detail->email) {
                                    $selected_member[$memberIndex]['project_id'] = $detail->project_id;
                                    $selected_member[$memberIndex]['is_team_leader'] = $detail->is_team_leader;
                                    $selected_member[$memberIndex]['view_project'] = $detail->view_project;
                                    $selected_member[$memberIndex]['edit_project'] = $detail->edit_project;
                                    $selected_member[$memberIndex]['email'] = $detail->email;
                                    $selected_member[$memberIndex]['user_id'] = $members->id;
                                    break;
                                }
                            }
                            $memberIndex++;
                        }
                    }

                    if (!empty($selected_member) && $project->deleted_at == null) {
                        ProjectMemberManagement::where('project_id', $project->id)->delete();

                        foreach ($selected_member as $memberData) {
                            $findUser = User::where('email', $memberData['email'])->first();
                            $userInviteStatusCheck = DB::connection('mysql2')->table('invite_data')->where(['module' => 'project', 'module_id' => $project->id, 'user_id' => $memberData['user_id']])->first();
                            $userInviteEmailStatus = config('constants.project_member_management_email_status.sent');
                            $userAccessLevel = config('constants.project_access_level.viewer');
                            if ($memberData['view_project'] == '1') {
                                $userAccessLevel = config('constants.project_access_level.viewer');
                            }

                            if ($memberData['edit_project'] == '1') {
                                $userAccessLevel = config('constants.project_access_level.editor');
                            }

                            if ($memberData['is_team_leader'] == '1') {
                                $userAccessLevel = config('constants.project_access_level.team_leader');
                            }

                            $userInviteStatus = config('constants.project_member_management_invite_status.invited');
                            if ($userInviteStatusCheck) {
                                switch ($userInviteStatusCheck->status) {
                                    case 'sent':
                                        $userInviteStatus = config('constants.project_member_management_invite_status.invited');
                                        break;

                                    case 'accepted':
                                        $userInviteStatus = config('constants.project_member_management_invite_status.accepted');
                                        break;

                                    case 'rejected':
                                        $userInviteStatus = config('constants.project_member_management_invite_status.declined');
                                        break;
                                }
                            }

                            if ($project->user_id == $memberData['user_id']) {
                                $userInviteStatus = config('constants.project_member_management_invite_status.accepted');
                            }

                            $newProjectMember = new ProjectMemberManagement();
                            $newProjectMember->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                            $newProjectMember->project_id = $project->id;
                            $newProjectMember->inviter_id = $project->user_id;
                            $newProjectMember->email = $memberData['email'];
                            $newProjectMember->invitee_name = $findUser != null ? $findUser->full_name : null;
                            $newProjectMember->invite_type = '1';
                            $newProjectMember->invite_status = $userInviteStatus;
                            $newProjectMember->email_status = $userInviteEmailStatus;
                            $newProjectMember->inviter_access_level = $userAccessLevel;
                            $newProjectMember->save();
                        }
                    }

                    // For project external links
                    $projectExternalLinks = DB::connection('mysql2')->table('user_sociallink')->where('project_id', $project->id)->get();
                    if ($projectExternalLinks->isNotEmpty()) {
                        ProjectExternalLink::where('project_id', $project->id)->delete();
                        foreach ($projectExternalLinks as $projectLink) {
                            if ($projectLink->link_url != null) {
                                $createdAt = $projectLink->created_at != null ? Carbon::createFromTimestamp($projectLink->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $updatedAt = $projectLink->updated_at != null ? Carbon::createFromTimestamp($projectLink->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $deletedAt = $projectLink->deleted_at != null ? Carbon::createFromTimestamp($projectLink->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $checkSocialLink = SocialLink::find($projectLink->social_link_id);

                                $newProjectExternalLink = new ProjectExternalLink();
                                $newProjectExternalLink->id = $projectLink->id;
                                $newProjectExternalLink->project_id = $project->id;
                                $newProjectExternalLink->social_media_link = $projectLink->link_url;
                                $newProjectExternalLink->social_link_id = $checkSocialLink != null ? $projectLink->social_link_id : '15';
                                $newProjectExternalLink->created_at = $createdAt;
                                $newProjectExternalLink->updated_at = $updatedAt;
                                $newProjectExternalLink->deleted_at = $deletedAt;
                                $newProjectExternalLink->save();
                            }
                        }
                    }

                    // For project files from 2(project_files, project_galleries) table to single one
                    $projectFiles = DB::connection('mysql2')->table('project_files')->where('project_id', $project->id)->get();
                    if ($projectFiles->isNotEmpty()) {
                        foreach ($projectFiles as $fileData) {
                            $extensionFetch = strtolower(pathinfo($fileData->original, PATHINFO_EXTENSION));
                            $fileType = $this->getFileType($extensionFetch);
                            if ($fileType != null) {
                                $createdAt = $fileData->created_at != null ? Carbon::createFromTimestamp($fileData->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $updatedAt = $fileData->updated_at != null ? Carbon::createFromTimestamp($fileData->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $deletedAt = $fileData->deleted_at != null ? Carbon::createFromTimestamp($fileData->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                switch ($fileType) {
                                    case 'image':
                                        $file_type = config('constants.project_file_type.image');
                                        break;

                                    case 'audio':
                                        $file_type = config('constants.project_file_type.audio');
                                        break;

                                    case 'document':
                                        $file_type = config('constants.project_file_type.docs');
                                        break;

                                    case 'video':
                                        $file_type = config('constants.project_file_type.video');
                                        break;
                                }
                                $newProjectFile = new ProjectFile();
                                $newProjectFile->project_id = $project->id;
                                $newProjectFile->title = $fileData->original;
                                $newProjectFile->path = $fileData->name;
                                $newProjectFile->type = $file_type;
                                $newProjectFile->created_at = $createdAt;
                                $newProjectFile->updated_at = $updatedAt;
                                $newProjectFile->deleted_at = $deletedAt;
                                $newProjectFile->save();
                            }
                        }
                    }

                    $projectGalleries = DB::connection('mysql2')->table('project_galleries')->where('project_id', $project->id)->get();
                    if ($projectGalleries->isNotEmpty()) {
                        foreach ($projectGalleries as $galleryData) {
                            $extensionFetch = strtolower(pathinfo($galleryData->original, PATHINFO_EXTENSION));
                            $fileType = $this->getFileType($extensionFetch);
                            if ($fileType != null) {
                                $createdAt = $galleryData->created_at != null ? Carbon::createFromTimestamp($galleryData->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $updatedAt = $galleryData->updated_at != null ? Carbon::createFromTimestamp($galleryData->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $deletedAt = $galleryData->deleted_at != null ? Carbon::createFromTimestamp($galleryData->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                switch ($fileType) {
                                    case 'image':
                                        $file_type = config('constants.project_file_type.image');
                                        break;

                                    case 'audio':
                                        $file_type = config('constants.project_file_type.audio');
                                        break;

                                    case 'document':
                                        $file_type = config('constants.project_file_type.docs');
                                        break;

                                    case 'video':
                                        $file_type = config('constants.project_file_type.video');
                                        break;
                                }
                                $newProjectFile = new ProjectFile();
                                $newProjectFile->project_id = $project->id;
                                $newProjectFile->title = $galleryData->original;
                                $newProjectFile->path = $galleryData->name;
                                $newProjectFile->type = $file_type;
                                $newProjectFile->created_at = $createdAt;
                                $newProjectFile->updated_at = $updatedAt;
                                $newProjectFile->deleted_at = $deletedAt;
                                $newProjectFile->save();
                            }
                        }
                    }

                    // For project template id
                    if ($newProject->challenge_id != null) {
                        $challengePitchId = DB::connection('mysql2')->table('challenge_pitches')->where('challenge_id', $newProject->challenge_id)->value('pitch_template_id');
                        if ($challengePitchId != '0') {
                            $pitchtemplateData = DB::connection('mysql2')->table('pitch_templates')->where('id', $challengePitchId)->select('id', 'title')->first();
                        } else {
                            $pitchtemplateData = DB::connection('mysql2')->table('pitch_templates')->whereNull('challenge_id')->select('id', 'title')->first();
                        }

                        if ($pitchtemplateData) {
                            $pitchtemplate = DB::connection('mysql2')->table('project_pitch_values')->where(['pitch_template_id' => $pitchtemplateData->id, 'project_id' => $project->id])->first();
                            if (!$pitchtemplate) {
                                $pitchtemplate = DB::connection('mysql2')->table('project_task_values')->where(['pitch_template_id' => $pitchtemplateData->id, 'project_id' => $project->id])->first();
                            }

                            if ($pitchtemplate && $pitchtemplate->pitch_template_id) {
                                $checkPitchTemplate = PitchTemplate::find($pitchtemplate->pitch_template_id);
                                if ($checkPitchTemplate) {
                                    $projectTemplate = new ProjectTemplate();
                                    $projectTemplate->project_id = $project->id;
                                    $projectTemplate->template_id = $checkPitchTemplate->id;
                                    $projectTemplate->save();
                                }
                            }
                        }
                    }

                    // For project pitch values
                    $projectPitchValues = DB::connection('mysql2')->table('project_pitch_values')->where(['project_id' => $project->id])->get();
                    if ($projectPitchValues->isNotEmpty()) {
                        foreach ($projectPitchValues as $pitchValue) {
                            $checkPitchTemplate = PitchTemplate::find($pitchValue->pitch_template_id);
                            if ($checkPitchTemplate) {
                                $getPitchData = ChallengePitch::find($pitchValue->pitch_id);
                                if ($getPitchData) {
                                    $createdAt = $pitchValue->created_at != null ? Carbon::createFromTimestamp($pitchValue->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $updatedAt = $pitchValue->updated_at != null ? Carbon::createFromTimestamp($pitchValue->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $deletedAt = $pitchValue->deleted_at != null ? Carbon::createFromTimestamp($pitchValue->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                    $newProjectPitchValue = new ProjectPitchValue();
                                    $newProjectPitchValue->id = $pitchValue->id;
                                    $newProjectPitchValue->project_id = $project->id;
                                    $newProjectPitchValue->pitch_template_id = $getPitchData->template_id;
                                    $newProjectPitchValue->project_pitch_id = $getPitchData->id;
                                    $newProjectPitchValue->description = $pitchValue->description ?? null;
                                    $newProjectPitchValue->created_at = $createdAt;
                                    $newProjectPitchValue->updated_at = $updatedAt;
                                    $newProjectPitchValue->deleted_at = $deletedAt;
                                    $newProjectPitchValue->save();
                                }
                            }
                        }
                    }

                    // For project task values
                    $projectTaskValues = DB::connection('mysql2')->table('project_task_values')->where(['project_id' => $project->id])->get();
                    if ($projectTaskValues->isNotEmpty()) {
                        foreach ($projectTaskValues as $taskValue) {
                            $checkPitchTemplate = PitchTemplate::find($taskValue->pitch_template_id);
                            if ($checkPitchTemplate) {
                                $getTaskData = ChallengeTask::find($taskValue->project_task_id);
                                if ($getTaskData) {
                                    $completedAt = $taskValue->complete_datetime != null ? Carbon::createFromTimestamp($taskValue->complete_datetime)->translatedFormat('Y-m-d H:i:s') : null;
                                    $createdAt = $taskValue->created_at != null ? Carbon::createFromTimestamp($taskValue->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $updatedAt = $taskValue->updated_at != null ? Carbon::createFromTimestamp($taskValue->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $deletedAt = $taskValue->deleted_at != null ? Carbon::createFromTimestamp($taskValue->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                    $newProjectTaskValue = new ProjectTaskValue();
                                    $newProjectTaskValue->id = $taskValue->id;
                                    $newProjectTaskValue->project_id = $project->id;
                                    $newProjectTaskValue->task_template_id = $getTaskData->template_id;
                                    $newProjectTaskValue->project_task_id = $getTaskData->id;
                                    $newProjectTaskValue->status = $taskValue->is_completed == '1' ? '1' : '0';
                                    $newProjectTaskValue->completed_date = $completedAt;
                                    $newProjectTaskValue->created_at = $createdAt;
                                    $newProjectTaskValue->updated_at = $updatedAt;
                                    $newProjectTaskValue->deleted_at = $deletedAt;
                                    $newProjectTaskValue->save();
                                }
                            }
                        }
                    }

                    // For project votes
                    $projectVotes = DB::connection('mysql2')->table('project_votes')->where(['project_id' => $project->id])->get();
                    if ($projectVotes->isNotEmpty()) {
                        foreach ($projectVotes as $projectVote) {
                            $checkUser = User::find($projectVote->user_id);
                            if ($checkUser && $projectVote->vote == '1') {
                                $createdAt = $projectVote->created_at != null ? Carbon::createFromTimestamp($projectVote->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $updatedAt = $projectVote->updated_at != null ? Carbon::createFromTimestamp($projectVote->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $deletedAt = $projectVote->deleted_at != null ? Carbon::createFromTimestamp($projectVote->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                $newProjectVote = new ProjectSocialActivity();
                                $newProjectVote->id = $projectVote->id;
                                $newProjectVote->user_id = $projectVote->user_id;
                                $newProjectVote->project_id = $project->id;
                                $newProjectVote->vote = '1';
                                $newProjectVote->created_at = $createdAt;
                                $newProjectVote->updated_at = $updatedAt;
                                $newProjectVote->deleted_at = $deletedAt;
                                $newProjectVote->save();
                            }
                        }
                    }
                }
            });

            DB::commit();
            $this->info('Migrating of old data for Challanges table completed.');

            // return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }

    private function getFileType($extension)
    {
        try {
            $imageExtensions = ['jpg', 'jpeg', 'webp', 'png'];
            $audioExtensions = ['mp3'];
            $docExtensions = ['pdf', 'doc', 'docx', 'xlsx', 'xls', 'pptx', 'pptm', 'odp', 'ppt'];
            $videoExtensions = ['mp4', 'mov', 'wmv', 'avi', 'webm', 'mkv', 'mpeg-2'];

            if (in_array($extension, $imageExtensions)) {
                return 'image';
            } elseif (in_array($extension, $audioExtensions)) {
                return 'audio';
            } elseif (in_array($extension, $docExtensions)) {
                return 'document';
            } elseif (in_array($extension, $videoExtensions)) {
                return 'video';
            }

            return null; // Return null if no valid type is found
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error($e->getMessage());

            return null;
        }
    }
}
