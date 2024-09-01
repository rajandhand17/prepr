<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Challenge as ModelChallenge;
use App\Models\ChallengeAchievement;
use App\Models\ChallengeAssessment;
use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeCustomTimelines;
use App\Models\ChallengeFlexibleAnnouncement;
use App\Models\ChallengeProjectTemplate;
use App\Models\ChallengeRequirement;
use App\Models\ChallengeSkillsGroupsStack;
use App\Models\ChallengeSponsor;
use App\Models\ChallengeTimelines;
use App\Models\ChallengeTypeMode;
use App\Models\Host;
use App\Models\Organization;
use App\Models\ProjectSubmissionRequirement;
use App\Models\Scorm;
use App\Models\ScormSco;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Challenge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:challenge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old Challanges table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for Challenges table started.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('challanges')->chunkById(1000, function ($challenges) {
                foreach ($challenges as $key => $challenge) {
                    $checkUser = User::find($challenge->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $checkOrganization = Organization::find($challenge->organisation);
                    if (!$checkOrganization) {
                        continue;
                    }

                    $category = '1';
                    if ($challenge->category != '0' && $challenge->category != null) {
                        $checkOldCategory = DB::connection('mysql2')->table('categories')->find($challenge->category);
                        if ($checkOldCategory) {
                            $checkCategory = Category::where('title', $checkOldCategory->name)->first();
                            if ($checkCategory) {
                                $category = $checkCategory->id;
                            }
                        }
                    }

                    $descriptionType = config('constants.description_type.text');
                    $checkScrom = DB::connection('mysql2')->table('scorm')->where(['resource_id' => $challenge->id,  'resource_type' => 'App\Models\Challange'])->whereNotNull('resource_type')->first();
                    if ($checkScrom) {
                        $descriptionType = config('constants.description_type.scorm');
                    }

                    // For main Challenges table
                    $checkChallenge = ModelChallenge::find($challenge->id);
                    if ($checkChallenge) {
                        $newChallenge = $checkChallenge;
                    } else {
                        $newChallenge = new ModelChallenge();
                    }

                    switch ($challenge->status) {
                        case 'open':
                            $challengeStatus = '0';
                            break;

                        case 'closed':
                            $challengeStatus = '1';
                            break;

                        case 'completed':
                            $challengeStatus = '2';
                            break;

                        default:
                            $challengeStatus = '2';
                            break;
                    }

                    switch ($challenge->privacy) {
                        case 'public':
                            $challengePrivacy = '0';
                            break;
                        case 'private':
                            $challengePrivacy = '1';
                            break;
                        default:
                            $challengePrivacy = '0';
                            break;
                    }
                    switch ($challenge->project_privacy) {
                        case 'public':
                            $challengeProjectPrivacy = '0';
                            break;
                        case 'private':
                            $challengeProjectPrivacy = '1';
                            break;
                        default:
                            $challengeProjectPrivacy = '1';
                            break;
                    }

                    switch ($challenge->published) {
                        case 'published':
                            $challengePublishedStatus = '1';
                            break;
                        case 'draft':
                            $challengePublishedStatus = '0';
                            break;
                        case 'archive':
                            $challengePublishedStatus = '2';
                            break;
                        default:
                            $challengePublishedStatus = '0';
                            break;
                    }

                    switch ($challenge->is_auto_created) {
                        case '0':
                            $challengeAutoCreated = '0';
                            break;
                        case '1':
                            $challengeAutoCreated = '1';
                            break;
                        default:
                            $challengeAutoCreated = '0';
                            break;
                    }

                    switch ($challenge->is_ai_created ?? '0') {
                        case '0':
                            $challengeAiCreated = '0';
                            break;
                        case '1':
                            $challengeAiCreated = '1';
                            break;
                        default:
                            $challengeAiCreated = '0';
                            break;
                    }

                    switch ($challenge->notify_participants) {
                        case 'send':
                            $challengeNotifyParticipants = '1';
                            break;
                        default:
                            $challengeNotifyParticipants = '0';
                            break;
                    }

                    $mediaType = 'image';
                    switch ($challenge->mediaType) {
                        case 'image':
                            $mediaType = 'image';
                            break;
                        case 'embeddedCode':
                            $mediaType = 'embedded';
                            break;
                        default:
                            $mediaType = 'image';
                            break;
                    }

                    $getTagGroups = DB::connection('mysql2')->table('manage_tag_group')->where(['module_id' => $challenge->id, 'module_type' => 'challenge']);
                    // Clone the query to avoid modifying the original
                    $getDuration = clone $getTagGroups;
                    $duration = $getDuration->where('group_type', 'duration')->pluck('group_tag_id')->first();
                    $duration_id = null;
                    if ($duration) {
                        if ($duration == '["169"]') {
                            $duration_id = '1';
                        } elseif ($duration == '["170"]') {
                            $duration_id = '2';
                        } elseif ($duration == '["171"]') {
                            $duration_id = '3';
                        } elseif ($duration == '["172"]') {
                            $duration_id = '4';
                        } elseif ($duration == '["173"]') {
                            $duration_id = '5';
                        } elseif ($duration == '["174"]') {
                            $duration_id = '6';
                        }
                    }
                    $getLevel = clone $getTagGroups;
                    $level = $getLevel->where('group_type', 'level')->pluck('group_tag_id')->first();
                    $level_id = null;
                    if ($level) {
                        if ($level == '["157"]') {
                            $level_id = '1';
                        } elseif ($level == '["158"]') {
                            $level_id = '2';
                        } elseif ($level == '["159"]') {
                            $level_id = '3';
                        } elseif ($level == '["160"]') {
                            $level_id = '4';
                        }
                    }

                    $createdAt = $challenge->created_at != null ? Carbon::createFromTimestamp($challenge->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                    $updatedAt = $challenge->updated_at != null ? Carbon::createFromTimestamp($challenge->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                    $deletedAt = $challenge->deleted_at != null ? Carbon::createFromTimestamp($challenge->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                    $newChallenge->id = $challenge->id;
                    $newChallenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newChallenge->language = $challenge->language;
                    $newChallenge->user_id = $challenge->user_id;
                    $newChallenge->organization_id = $challenge->organisation;
                    $newChallenge->category_id = $category;
                    $newChallenge->duration_id = $duration_id;
                    $newChallenge->level_id = $level_id;
                    $newChallenge->slug = $challenge->slug;
                    $newChallenge->title = $challenge->title;
                    $newChallenge->description_type = $descriptionType;
                    $newChallenge->description = ($descriptionType == '1') ? null : $challenge->description;
                    $newChallenge->privacy = $challengePrivacy;
                    $newChallenge->media_type = $mediaType;
                    $newChallenge->media = $challenge->cover_image;
                    $newChallenge->status = $challengePublishedStatus;
                    $newChallenge->source_link = $challenge->sourcelink;
                    $newChallenge->agreement = $challenge->agreement;
                    $newChallenge->is_notification_enabled = $challengeNotifyParticipants;
                    $newChallenge->project_privacy = $challengeProjectPrivacy;
                    $newChallenge->is_open = $challengeStatus;
                    $newChallenge->is_auto_created = $challengeAutoCreated;
                    $newChallenge->is_ai_created = $challengeAiCreated;
                    $newChallenge->is_accessible = $challenge->is_accessable;
                    $newChallenge->total_share = $challenge->total_share;
                    $newChallenge->created_at = $createdAt;
                    $newChallenge->updated_at = $updatedAt;
                    $newChallenge->deleted_at = $deletedAt;
                    $newChallenge->save();

                    if ($checkScrom) {
                        $newScorm = new Scorm();
                        $newScorm->model_type = ModelChallenge::class;
                        $newScorm->id = $checkScrom->id;
                        $newScorm->model_id = $checkScrom->resource_id;
                        $newScorm->title = $checkScrom->title;
                        $newScorm->origin_file = $checkScrom->origin_file;
                        $newScorm->version = $checkScrom->version;
                        $newScorm->uuid = $checkScrom->uuid;
                        $newScorm->identifier = $checkScrom->identifier;
                        $newScorm->entry_url = $checkScrom->entry_url;
                        $newScorm->created_at = $checkScrom->created_at;
                        $newScorm->updated_at = $checkScrom->updated_at;
                        $newScorm->save();

                        $checkScromSco = DB::connection('mysql2')->table('scorm_sco')->where(['scorm_id' => $checkScrom->id])->first();
                        if ($checkScromSco) {
                            $newScormSco = new ScormSco();
                            $newScormSco->id = $checkScromSco->id;
                            $newScormSco->scorm_id = $checkScromSco->scorm_id;
                            $newScormSco->uuid = $checkScromSco->uuid;
                            $newScormSco->sco_parent_id = $checkScromSco->sco_parent_id;
                            $newScormSco->entry_url = $checkScromSco->entry_url;
                            $newScormSco->identifier = $checkScromSco->identifier;
                            $newScormSco->title = $checkScromSco->title;
                            $newScormSco->visible = $checkScromSco->visible;
                            $newScormSco->sco_parameters = $checkScromSco->sco_parameters;
                            $newScormSco->launch_data = $checkScromSco->launch_data;
                            $newScormSco->max_time_allowed = $checkScromSco->max_time_allowed;
                            $newScormSco->time_limit_action = $checkScromSco->time_limit_action;
                            $newScormSco->block = $checkScromSco->block;
                            $newScormSco->score_int = $checkScromSco->score_int;
                            $newScormSco->score_decimal = $checkScromSco->score_decimal;
                            $newScormSco->completion_threshold = $checkScromSco->completion_threshold;
                            $newScormSco->prerequisites = $checkScromSco->prerequisites;
                            $newScormSco->created_at = $checkScromSco->created_at;
                            $newScormSco->updated_at = $checkScromSco->updated_at;
                            $newScormSco->save();
                        }
                    }

                    // For Challenge Sponsers table
                    $arrayHost = json_decode($challenge->host_id, true);
                    if (!empty($arrayHost)) {
                        ChallengeSponsor::where('challenge_id', $challenge->id)->delete();
                        foreach (array_filter($arrayHost) as $host) {
                            $checkHost = Host::find($host);
                            if ($checkHost) {
                                $checkChallengeSponsor = ChallengeSponsor::where(['challenge_id' => $challenge->id, 'host_id' => $host])->first();
                                if (!$checkChallengeSponsor) {
                                    $challengeSponsor = new ChallengeSponsor();
                                    $challengeSponsor->challenge_id = $challenge->id;
                                    $challengeSponsor->host_id = $host;
                                    $challengeSponsor->save();
                                }
                            }
                        }
                    }

                    //for mode and type
                    $getMode = clone $getTagGroups;
                    $mode = $getMode->where('group_type', 'mode')->pluck('group_tag_id')->first();
                    if ($mode) {
                        $modes = json_decode($mode, true);
                        if (!empty($modes)) {
                            ChallengeTypeMode::where(['challenge_id' => $challenge->id, 'type_mode' => '1'])->delete();
                            $mode_id = null;
                            foreach ($modes as $single_mode) {
                                if ($single_mode == '196') {
                                    $mode_id = '4';
                                } elseif ($single_mode == '197') {
                                    $mode_id = '5';
                                }
                                if ($mode_id != null) {
                                    $challengeMode = new ChallengeTypeMode();
                                    $challengeMode->challenge_id = $challenge->id;
                                    $challengeMode->type_mode = '1';
                                    $challengeMode->value = $mode_id;
                                    $challengeMode->save();
                                }
                            }
                        }
                    }

                    $getType = clone $getTagGroups;
                    $type = $getType->where('group_type', 'type')->pluck('group_tag_id')->first();
                    if ($type) {
                        $types = json_decode($type, true);
                        if (!empty($types)) {
                            ChallengeTypeMode::where(['challenge_id' => $challenge->id, 'type_mode' => '0'])->delete();
                            $type_id = null;
                            foreach ($types as $single_type) {
                                if ($single_type == '192') {
                                    $type_id = '0';
                                } elseif ($single_type == '193') {
                                    $type_id = '1';
                                } elseif ($single_type == '194') {
                                    $type_id = '2';
                                } elseif ($single_type == '195') {
                                    $type_id = '3';
                                }
                                if ($type_id != null) {
                                    $challengeMode = new ChallengeTypeMode();
                                    $challengeMode->challenge_id = $challenge->id;
                                    $challengeMode->type_mode = '0';
                                    $challengeMode->value = $type_id;
                                    $challengeMode->save();
                                }
                            }
                        }
                    }

                    // For Challenge skils table
                    $arraySkills = json_decode($challenge->challange_skill, true);
                    if (!empty($arraySkills)) {
                        ChallengeSkillsGroupsStack::where(['challenge_id' => $challenge->id, 'foreign_id' => '0'])->delete();
                        foreach (array_filter($arraySkills) as $skill) {
                            $challengeSkill = new ChallengeSkillsGroupsStack();
                            $challengeSkill->challenge_id = $challenge->id;
                            $challengeSkill->foreign_id = (int) $skill;
                            $challengeSkill->type = '0';
                            $challengeSkill->save();
                        }
                    }

                    // For Challenge skils stack table
                    $skillStacks = $challenge->skill_stacks;
                    if (!empty($skillStacks)) {
                        ChallengeSkillsGroupsStack::where(['challenge_id' => $challenge->id, 'foreign_id' => '2'])->delete();
                        foreach (explode(',', $skillStacks) as $skillStack) {
                            $challengeSkillStack = new ChallengeSkillsGroupsStack();
                            $challengeSkillStack->challenge_id = $challenge->id;
                            $challengeSkillStack->foreign_id = (int) $skillStack;
                            $challengeSkillStack->type = '2';
                            $challengeSkillStack->save();
                        }
                    }

                    // For Challenge skils groups table
                    $skillGroups = $challenge->skill_groups;
                    if (!empty($skillGroups)) {
                        ChallengeSkillsGroupsStack::where(['challenge_id' => $challenge->id, 'foreign_id' => '1'])->delete();
                        foreach (explode(',', $skillGroups) as $skillGroup) {
                            $challengeSkillGroup = new ChallengeSkillsGroupsStack();
                            $challengeSkillGroup->challenge_id = $challenge->id;
                            $challengeSkillGroup->foreign_id = (int) $skillGroup;
                            $challengeSkillGroup->type = '1';
                            $challengeSkillGroup->save();
                        }
                    }

                    // For Challenge Requirements table
                    $checkChallengeRequirements = ChallengeRequirement::where('challenge_id', $challenge->id)->first();
                    if ($checkChallengeRequirements) {
                        $challengeRequirements = $checkChallengeRequirements;
                    } else {
                        $challengeRequirements = new ChallengeRequirement();
                    }

                    switch ($challenge->submitProject) {
                        case 'on':
                            $allowSubmitProject = '1';
                            break;
                        default:
                            $allowSubmitProject = '0';
                            break;
                    }

                    switch ($challenge->completeEducationProgram) {
                        case 'on':
                            $completeEducationProgram = '1';
                            break;
                        default:
                            $completeEducationProgram = '0';
                            break;
                    }

                    switch ($challenge->completeExperience) {
                        case 'on':
                            $completeExperience = '1';
                            break;
                        default:
                            $completeExperience = '0';
                            break;
                    }

                    $projectSubmissionRequirementIds = [];
                    $newRequirements = ProjectSubmissionRequirement::pluck('title', 'id')->map(function ($title) {
                        return strtolower(str_replace(' ', '', $title));
                    })->toArray();
                    $oldRequirements = json_decode($challenge->projectSubmissionRequirements);

                    foreach ($oldRequirements as $requirements) {
                        $projectSubmissionRequirementIds[] = json_encode(array_search(strtolower($requirements), $newRequirements));
                    }

                    $challengeRequirements->challenge_id = $challenge->id;
                    $challengeRequirements->min_rank = $challenge->min_ranks;
                    $challengeRequirements->min_points = $challenge->min_points;
                    $challengeRequirements->project_submission_requirement_ids = $projectSubmissionRequirementIds;
                    $challengeRequirements->max_project_submission = $challenge->maxProjectSubmission;
                    $challengeRequirements->max_project_associate = $challenge->maxAssociatedProjects;
                    $challengeRequirements->min_experience = $challenge->minExperience;
                    $challengeRequirements->min_imported_badges = $challenge->minImportedBadges;
                    $challengeRequirements->min_achievement_counts = $challenge->minAchievementTrophies;
                    $challengeRequirements->allow_submit_project = $allowSubmitProject;
                    $challengeRequirements->requirement_program = '0';
                    $challengeRequirements->complete_education_program = $completeEducationProgram;
                    $challengeRequirements->complete_experience = $completeExperience;
                    $challengeRequirements->additional_requirements = $challenge->additional_info;
                    $challengeRequirements->save();

                    // For Challenge Achievements table
                    $challengePrices = DB::connection('mysql2')->table('challange_prices')->where('challenge_id', $challenge->id)->whereNull('deleted_at')->get();
                    if ($challengePrices->isNotEmpty()) {
                        ChallengeAchievement::where('challenge_id', $challenge->id)->delete();
                        foreach ($challengePrices as $challengePrice) {
                            switch ($challengePrice->type) {
                                case 'incentive':
                                    $challengeAchievementType = '1';
                                    break;
                                case 'participation':
                                    $challengeAchievementType = '0';
                                    break;
                                default:
                                    $challengeAchievementType = '1';
                                    break;
                            }

                            $challengeAchievement = new ChallengeAchievement();
                            $challengeAchievement->challenge_id = $challengePrice->challenge_id;
                            $challengeAchievement->achievement_type = $challengeAchievementType;
                            $challengeAchievement->achievement_name = $challengePrice->name;
                            $challengeAchievement->achievement_prize = $challengePrice->prize;
                            $challengeAchievement->achievement_points = $challengePrice->points;
                            $challengeAchievement->achievement_image = $challengePrice->trophy;
                            $challengeAchievement->save();
                        }
                    }

                    // For Challenge Assessments table
                    $checkChallengeAssessments = DB::connection('mysql2')->table('challange_assessments')->where('challenge_id', $challenge->id)->whereNull('deleted_at')->get();
                    if ($checkChallengeAssessments->isNotEmpty()) {
                        foreach ($checkChallengeAssessments as $checkChallengeAssessment) {
                            switch ($checkChallengeAssessment->assessment_type) {
                                case 'closed':
                                    $challengeAssessmentType = '2';
                                    break;
                                case 'open':
                                    $challengeAssessmentType = '1';
                                    break;
                                case 'none':
                                    $challengeAssessmentType = '0';
                                    break;
                                default:
                                    $challengeAssessmentType = '0';
                                    break;
                            }

                            switch ($checkChallengeAssessment->visibility) {
                                case 'none':
                                    $challengeAssessmentVisibility = '0';
                                    break;
                                case 'hidden':
                                    $challengeAssessmentVisibility = '2';
                                    break;
                                case 'users':
                                    $challengeAssessmentVisibility = '1';
                                    break;
                                default:
                                    $challengeAssessmentVisibility = '0';
                                    break;
                            }

                            if ($challengeAssessmentType == '1' || $challengeAssessmentType == '2') {
                                $challengeAssessment = new ChallengeAssessment();
                                $challengeAssessment->id = $checkChallengeAssessment->id;
                                $challengeAssessment->challenge_id = $checkChallengeAssessment->challenge_id;
                                $challengeAssessment->assessment_type = $challengeAssessmentType;
                                $challengeAssessment->visibility = $challengeAssessmentVisibility;
                                $challengeAssessment->members_email = $checkChallengeAssessment->members !== null ? json_decode($checkChallengeAssessment->members) : null;
                                $challengeAssessment->guidelines = $checkChallengeAssessment->guidline;
                                $challengeAssessment->attachments = $checkChallengeAssessment->attachment;
                                $challengeAssessment->save();
                            } elseif ($challengeAssessmentType == '0') {
                                $challengeAssessment = new ChallengeAssessment();
                                $challengeAssessment->id = $checkChallengeAssessment->id;
                                $challengeAssessment->challenge_id = $checkChallengeAssessment->challenge_id;
                                $challengeAssessment->assessment_type = $challengeAssessmentType;
                                $challengeAssessment->visibility = '0';
                                $challengeAssessment->members_email = null;
                                $challengeAssessment->guidelines = null;
                                $challengeAssessment->attachments = null;
                                $challengeAssessment->save();
                            }

                            // For Challenge Assessments criterias table
                            $checkChallengeAssessmentCriterias = DB::connection('mysql2')->table('challange_assessment_criterias')->where('challenge_assessment_id', $checkChallengeAssessment->id)->whereNull('deleted_at')->get();
                            if ($checkChallengeAssessmentCriterias->isNotEmpty()) {
                                foreach ($checkChallengeAssessmentCriterias as $challengeAssessmentCriteriaOld) {
                                    if ($challengeAssessmentCriteriaOld->challenge_assessment_id) {
                                        $challengeAssessmentCriteria = new ChallengeAssessmentCriteria();
                                        $challengeAssessmentCriteria->challenge_id = $challenge->id;
                                        $challengeAssessmentCriteria->assessment_id = $challengeAssessmentCriteriaOld->challenge_assessment_id;
                                        $challengeAssessmentCriteria->title = $challengeAssessmentCriteriaOld->title;
                                        $challengeAssessmentCriteria->score = $challengeAssessmentCriteriaOld->score;
                                        $challengeAssessmentCriteria->weight = $challengeAssessmentCriteriaOld->weight;
                                        $challengeAssessmentCriteria->save();
                                    }
                                }
                            }
                        }
                    }

                    // For Challenge Project Template table
                    $checkChallengeProjectTemplates = DB::connection('mysql2')->table('challenge_pitches')->where('challenge_id', $challenge->id)->whereNull('deleted_at')->get();
                    if ($checkChallengeProjectTemplates->isNotEmpty()) {
                        ChallengeProjectTemplate::where('challenge_id', $challenge->id)->delete();
                        foreach ($checkChallengeProjectTemplates as $checkChallengeProjectTemplate) {
                            $challengeProjectTemplate = new ChallengeProjectTemplate();
                            $challengeProjectTemplate->challenge_id = $checkChallengeProjectTemplate->challenge_id;
                            $challengeProjectTemplate->template_id = $checkChallengeProjectTemplate->pitch_template_id;
                            $challengeProjectTemplate->save();
                        }
                    }

                    // For Challenge Custom Timeline table
                    $checkChallengeCustomTimelines = DB::connection('mysql2')->table('challenge_custom_time')->where('challenge_id', $challenge->id)->whereNull('deleted_at')->get();
                    if ($checkChallengeCustomTimelines->isNotEmpty()) {
                        ChallengeCustomTimelines::where('challenge_id', $challenge->id)->delete();
                        foreach ($checkChallengeCustomTimelines as $checkChallengeCustomTimeline) {
                            switch ($checkChallengeCustomTimeline->scheduleAnnouncement) {
                                case 'yes':
                                    $challengeScheduleNotify = '1';
                                    break;
                                case 'no':
                                    $challengeScheduleNotify = '0';
                                    break;
                                default:
                                    $challengeScheduleNotify = '0';
                                    break;
                            }

                            switch ($checkChallengeCustomTimeline->customDateDuration) {
                                case 'days':
                                    $challengeDateDuration = 'days';
                                    break;
                                case 'weeks':
                                    $challengeDateDuration = 'weeks';
                                    break;
                                case 'months':
                                    $challengeDateDuration = 'months';
                                    break;
                                default:
                                    $challengeDateDuration = 'days';
                                    break;
                            }
                            $challengeCustomTimeline = new ChallengeCustomTimelines();
                            $challengeCustomTimeline->challenge_id = $checkChallengeCustomTimeline->challenge_id;
                            $challengeCustomTimeline->custom_timelines_title = $checkChallengeCustomTimeline->title;
                            $challengeCustomTimeline->custom_timelines_number = $checkChallengeCustomTimeline->customDateNumber;
                            $challengeCustomTimeline->custom_timelines_description = $checkChallengeCustomTimeline->description;
                            $challengeCustomTimeline->custom_timelines_duration = $challengeDateDuration;
                            $challengeCustomTimeline->schedule_custom_notify = $challengeScheduleNotify;
                            $challengeCustomTimeline->save();

                            // For Challenge Flexible Announcements table
                            $checkFlexibleAnnouncements = DB::connection('mysql2')->table('flexible_announcement')->where('customDateId', $checkChallengeCustomTimeline->id)->get();
                            if (!empty($checkFlexibleAnnouncements)) {
                                foreach ($checkFlexibleAnnouncements as $flexibleAnnouncement) {
                                    $challengeFlexibleAnnouncement = ChallengeFlexibleAnnouncement::where('challenge_custom_timeline_id', $flexibleAnnouncement->customDateId)->first();
                                    if ($challengeFlexibleAnnouncement) {
                                        $oldChallengeRequirement = $challengeFlexibleAnnouncement;
                                    } else {
                                        $oldChallengeRequirement = new ChallengeFlexibleAnnouncement();
                                    }

                                    if ($flexibleAnnouncement->sent_status == 'email') {
                                        $challengeFlexibleAnnouncementType = '0';
                                    } else {
                                        $challengeFlexibleAnnouncementType = '1';
                                    }

                                    if ($flexibleAnnouncement->schedule_status == 'immediately') {
                                        $custom_announcement_schedule_status = '0';
                                    } elseif ($flexibleAnnouncement->schedule_status == 'custome') {
                                        $custom_announcement_schedule_status = '1';
                                    }

                                    $oldChallengeRequirement->challenge_id = $flexibleAnnouncement->challenge_id;
                                    $oldChallengeRequirement->challenge_custom_timeline_id = (int) $flexibleAnnouncement->customDateId;
                                    $oldChallengeRequirement->custom_announcement_type = $challengeFlexibleAnnouncementType ?? null;
                                    $oldChallengeRequirement->custom_announcement_number = $flexibleAnnouncement->announcementNumber ?? null;
                                    $oldChallengeRequirement->custom_announcement_title = $flexibleAnnouncement->title ?? null;
                                    $oldChallengeRequirement->custom_announcement_description = $flexibleAnnouncement->body ?? null;
                                    $oldChallengeRequirement->custom_announcement_schedule_status = $custom_announcement_schedule_status;
                                    $oldChallengeRequirement->save();
                                }
                            }
                        }
                    }

                    // For Challenge Timelines table
                    $checkchallengeTimelines = ChallengeTimelines::where('challenge_id', $challenge->id)->first();
                    if ($checkchallengeTimelines) {
                        $challengeTimelines = $checkchallengeTimelines;
                    } else {
                        $challengeTimelines = new ChallengeTimelines();
                    }

                    switch ($challenge->dates) {
                        case 'flexible':
                            $challengeTimelineType = '0';
                            break;
                        case 'restricted':
                            $challengeTimelineType = '1';
                            break;
                        default:
                            $challengeTimelineType = null;
                            break;
                    }

                    if ($challenge->length == null) {
                        $openDate = $challenge->call_date;
                        $submissionDate = $challenge->deadline;
                        $open_date = Carbon::parse($openDate);
                        $close_date = Carbon::parse($submissionDate);
                        $challenge_duration = $open_date->diffInDays($close_date);
                    }

                    $challengeAutoAlert = '0';
                    if ($challenge->automaticAlert != null) {
                        switch (implode(json_decode($challenge->automaticAlert))) {
                            case 'beforeDay':
                                $challengeAutoAlert = '0';
                                break;
                            case 'beforeWeek':
                                $challengeAutoAlert = '1';
                                break;
                            default:
                                $challengeAutoAlert = '0';
                                break;
                        }
                    }

                    $challengeTimelines->challenge_id = $challenge->id;
                    $challengeTimelines->timeline_type = $challengeTimelineType;
                    $challengeTimelines->start_date = date('Y-m-d H:i:s', strtotime($challenge->call_date));
                    $challengeTimelines->start_date_description = $challenge->call_date_desc ?? 'Start of Challenge';
                    $challengeTimelines->registration_deadline_date = date('Y-m-d H:i:s', strtotime($challenge->application_deadline));
                    $challengeTimelines->registration_deadline_date_description = $challenge->application_dateline_desc ?? 'Last day of registration deadline';
                    $challengeTimelines->submission_deadline_date = date('Y-m-d H:i:s', strtotime($challenge->deadline));
                    $challengeTimelines->submission_deadline_date_description = $challenge->submission_deadline_date_desc ?? 'Last date of Challenge Submissioon';
                    $challengeTimelines->challenge_duration = ($challenge->length != null) ? $challenge->length : $challenge_duration;
                    $challengeTimelines->flexible_date_number = $challenge->flexibleDateNumber;
                    $challengeTimelines->flexible_date_duration = $challenge->flexibleExpireDateDuration;
                    $challengeTimelines->automatic_alert = $challengeAutoAlert;
                    $challengeTimelines->flexible_expire_deadline = date('Y-m-d H:i:s', strtotime($challenge->flexibleExpireDate));
                    $challengeTimelines->save();
                }
            });

            DB::commit();
            $this->info('Migrating of old data for Challanges table completed.');

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
