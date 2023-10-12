<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Category;
use App\Models\Challenge as ModelChallenge;
use App\Models\ChallengeAchievement;
use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeRequirement;
use App\Models\ChallengeSkillsGroupsStack;
use App\Models\ChallengeSponsor;
use App\Models\ChallengeTagsGroups;
use App\Models\Organization;
use App\Models\ProjectSubmissionRequirement;
use App\Models\User;
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
    protected $description = 'This command is use to migrate old challanges table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for challenges table started.');
            $challenges = DB::connection('mysql2')->table('challanges')->get();
            if ($challenges->count() > 0) {
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
                        $checkCategory = Category::where('title', $checkOldCategory->name)->first();
                        if ($checkCategory) {
                            $category = $checkCategory->id;
                        }
                    }

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

                    switch ($challenge->notify_participants) {
                        case 'send':
                            $challengeNotifyParticipants = '1';
                            break;
                        default:
                            $challengeNotifyParticipants = '0';
                            break;
                    }

                    $newChallenge->id = $challenge->id;
                    $newChallenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newChallenge->language = $challenge->language;
                    $newChallenge->user_id = $challenge->user_id;
                    $newChallenge->organization_id = $challenge->organisation;
                    $newChallenge->category_id = $category;
                    $newChallenge->duration_id = '1';
                    $newChallenge->level_id = '1';
                    $newChallenge->slug = $challenge->slug;
                    $newChallenge->title = $challenge->title;
                    $newChallenge->description = $challenge->description;
                    $newChallenge->privacy = $challengePrivacy;
                    $newChallenge->media_type = $challenge->mediaType;
                    $newChallenge->media = $challenge->cover_image;
                    $newChallenge->status = $challengePublishedStatus;
                    $newChallenge->source_link = $challenge->sourcelink;
                    $newChallenge->agreement = $challenge->agreement;
                    $newChallenge->is_notification_enabled = $challengeNotifyParticipants;
                    $newChallenge->project_privacy = $challengeProjectPrivacy;
                    $newChallenge->is_open = $challengeStatus;
                    $newChallenge->is_auto_created = $challengeAutoCreated;
                    $newChallenge->save();

                    // For Challenge Host/Sponser
                    $arrayHost = json_decode($challenge->host_id, true);
                    if (!empty($arrayHost)) {
                        foreach (array_filter($arrayHost) as $host) {
                            $checkHost = ChallengeSponsor::find($host);
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

                    // For Challenge Skill
                    $arraySkills = json_decode($challenge->challange_skill, true);
                    if (!empty($arraySkills)) {
                        foreach (array_filter($arraySkills) as $skill) {
                            $challengeSkill = new ChallengeSkillsGroupsStack();
                            $challengeSkill->challenge_id = $challenge->id;
                            $challengeSkill->foreign_id = $skill;
                            $challengeSkill->type = '0';
                            $challengeSkill->save();
                        }
                    }

                    // For Challenge Tag
                    $arrayTags = json_decode($challenge->challange_tag, true);
                    if (!empty($arrayTags)) {
                        foreach (array_filter($arrayTags) as $tag) {
                            $challengeTag = new ChallengeTagsGroups();
                            $challengeTag->challenge_id = $challenge->id;
                            $challengeTag->foreign_id = $tag;
                            $challengeTag->type = '0';
                            $challengeTag->save();
                        }
                    }

                    // For Challenge Requirements
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

                    // For Challenge Achievements
                    $challengePrices = DB::connection('mysql2')->table('challange_prices')->where('challenge_id', $challenge->id)->whereNull('deleted_at')->get();
                    if ($challengePrices->isNotEmpty()) {
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

                    // For Challenge Assessment Criteria
                    $challengeAssessmentCriterias = DB::connection('mysql2')->table('challange_assessment_criterias')->where('challenge_assessment_id', $challenge->id)->whereNull('deleted_at')->get();
                    if ($challengeAssessmentCriterias->isNotEmpty()) {
                        foreach ($challengeAssessmentCriterias as $challengeAssessmentCriteria) {
                            $challengeAssessment = new ChallengeAssessmentCriteria();
                            $challengeAssessment->challenge_id = $challengeAssessmentCriteria->challenge_assessment_id;
                            $challengeAssessment->title = $challengeAssessmentCriteria->title;
                            $challengeAssessment->score = $challengeAssessmentCriteria->score;
                            $challengeAssessment->weight = $challengeAssessmentCriteria->weight;
                            $challengeAssessment->save();
                        }
                    }

                }
            }

            return;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }
}
