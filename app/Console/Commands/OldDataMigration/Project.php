<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Lab;
use App\Models\Project as ModelsProject;
use App\Models\ProjectAdditionalInfo;
use App\Models\ProjectIndustry;
use App\Models\ProjectMemberManagement;
use App\Models\ProjectSkill;
use App\Models\ProjectStage;
use App\Models\ProjectStatus;
use App\Models\ProjectType;
use App\Models\ProjectVertical;
use App\Models\Skill;
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
}
