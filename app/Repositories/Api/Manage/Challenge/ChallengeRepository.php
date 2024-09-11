<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Helpers\UtilityHelper;
use App\Jobs\MixpenalJob;
use App\Models\Challenge;
use App\Repositories\Api\Manage\Scorm\ScormRepository;
use App\Services\AchievementService;
use App\Services\Manage\AIService;
use App\Services\Manage\CampusConnectOpportunityService;
use App\Services\Manage\CampusConnectStoryService;
use App\Services\Manage\ChallengeAchievementService;
use App\Services\Manage\ChallengeAnnouncementService;
use App\Services\Manage\ChallengeAssessmentCriteriaService;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\ChallengeCustomTimelinesService;
use App\Services\Manage\ChallengeExternalLinkService;
use App\Services\Manage\ChallengeJobsService;
use App\Services\Manage\ChallengeProjectTemplateService;
use App\Services\Manage\ChallengeRequirementService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeSkillsGroupsStackService;
use App\Services\Manage\ChallengeSponsorService;
use App\Services\Manage\ChallengeTimelinesService;
use App\Services\Manage\ChallengeTypeModeService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\ProjectPitchService;
use App\Services\ProjectService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;
    private $scormRepository;
    private $challengeAchievementService;
    private $challengeSponsorService;
    private $challengeSkillsGroupsStackService;
    private $challengeJobsService;
    private $challengeRequirementService;
    private $challengeAssessmentCriteriaService;
    private $challengeProjectTemplateService;
    private $challengeAssessmentService;
    private $challengeTimelinesService;
    private $challengeCustomTimelinesService;
    private $challengeExternalLinkService;
    private $challengeAnnouncementService;
    private $aiService;
    private $componentAssociationService;
    private $projectPitchService;
    private $campusConnectOpportunityService;
    private $campusConnectStoryService;
    private $achievementService;
    private $challengeTypeModeService;
    private $projectService;

    public function __construct(ChallengeService $challengeService, ScormRepository $scormRepository, ChallengeAchievementService $challengeAchievementService, ChallengeSponsorService $challengeSponsorService, ChallengeSkillsGroupsStackService $challengeSkillsGroupsStackService, ChallengeRequirementService $challengeRequirementService, ChallengeAssessmentCriteriaService $challengeAssessmentCriteriaService, ChallengeProjectTemplateService $challengeProjectTemplateService, ChallengeAssessmentService $challengeAssessmentService, ChallengeTimelinesService $challengeTimelinesService, ChallengeCustomTimelinesService $challengeCustomTimelinesService, ChallengeExternalLinkService $challengeExternalLinkService, ChallengeAnnouncementService $challengeAnnouncementService, ChallengeJobsService $challengeJobsService, AIService $aiService, ComponentAssociationService $componentAssociationService, ProjectPitchService $projectPitchService, CampusConnectOpportunityService $campusConnectOpportunityService, CampusConnectStoryService $campusConnectStoryService, AchievementService $achievementService, ChallengeTypeModeService $challengeTypeModeService, ProjectService $projectService)
    {
        $this->challengeService = $challengeService;
        $this->scormRepository = $scormRepository;
        $this->challengeAchievementService = $challengeAchievementService;
        $this->challengeSponsorService = $challengeSponsorService;
        $this->challengeSkillsGroupsStackService = $challengeSkillsGroupsStackService;
        $this->challengeJobsService = $challengeJobsService;
        $this->challengeRequirementService = $challengeRequirementService;
        $this->challengeAssessmentCriteriaService = $challengeAssessmentCriteriaService;
        $this->challengeProjectTemplateService = $challengeProjectTemplateService;
        $this->challengeAssessmentService = $challengeAssessmentService;
        $this->challengeTimelinesService = $challengeTimelinesService;
        $this->challengeCustomTimelinesService = $challengeCustomTimelinesService;
        $this->challengeExternalLinkService = $challengeExternalLinkService;
        $this->challengeAnnouncementService = $challengeAnnouncementService;
        $this->componentAssociationService = $componentAssociationService;
        $this->aiService = $aiService;
        $this->projectPitchService = $projectPitchService;
        $this->campusConnectOpportunityService = $campusConnectOpportunityService;
        $this->campusConnectStoryService = $campusConnectStoryService;
        $this->achievementService = $achievementService;
        $this->challengeTypeModeService = $challengeTypeModeService;
        $this->projectService = $projectService;
    }

    public function getChallengeCountBasedOnOrganization($organizationId)
    {
        try {
            return $this->challengeService->getChallengeCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeList($request, $organization)
    {
        try {
            return $this->challengeService->getChallengeList($request, $organization);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadChallengeCoverImage($image)
    {
        try {
            return $this->challengeService->uploadChallengeCoverImage($image);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadChallengeAssessment($attachment)
    {
        try {
            return $this->challengeAssessmentService->uploadChallengeAssessment($attachment);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallenge($request, $uploaded_cover_image, $uploaded_achievement_image, $uploaded_assessment_attachment, $organizationData)
    {
        try {
            $createChallenge = DB::transaction(function () use ($request, $uploaded_cover_image, $uploaded_achievement_image, $uploaded_assessment_attachment, $organizationData) {
                $updateChallengeDescription = true;
                $createChallenge = $this->challengeService->createChallenge($request, $uploaded_cover_image, $organizationData->id);
                if ($request->description_type == 'scorm') {
                    $updateChallengeDescription = $this->scormRepository->upload(Challenge::class, $createChallenge->id, $request->file('scorm_file'), $createChallenge->scorm);
                }
                $createChallengeAchievement = $this->challengeAchievementService->createChallengeAchievement($request, $createChallenge->id, $uploaded_achievement_image);
                $createChallengeTypeMode = $this->challengeTypeModeService->storeChallengeTypeMode($request, $createChallenge->id);
                $createChallengeSponsor = $this->challengeSponsorService->createChallengeSponsor($request, $createChallenge->id);
                $createChallengeJobs = $this->challengeJobsService->createChallengeJobs($request, $createChallenge->id);
                $createChallengeSkillsGroupsStack = $this->challengeSkillsGroupsStackService->createChallengeSkillsGroupsStack($request, $createChallenge->id);
                $createChallengeRequirement = $this->challengeRequirementService->createChallengeRequirement($request, $createChallenge->id);
                $createChallengeAssessment = $this->challengeAssessmentService->createChallengeAssessment($request, $createChallenge->id, $uploaded_assessment_attachment);
                $createChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->createChallengeAssessmentCriteria($request, $createChallenge->id, $createChallengeAssessment);
                $createChallengeProjectTemplate = $this->challengeProjectTemplateService->createChallengeProjectTemplate($request, $createChallenge->id);
                $createChallengeTimelines = $this->challengeTimelinesService->createChallengeTimelines($request, $createChallenge->id);
                $createChallengeCustomTimelines = $this->challengeCustomTimelinesService->createChallengeCustomTimelines($request, $createChallenge->id);
                $createChallengeExternalLink = $this->challengeExternalLinkService->createChallengeExternalLink($request, $createChallenge->id);
                $createChallengeComponentAssociation = $this->componentAssociationService->createChallengeComponentAssociation($request, $createChallenge->id);

                $campusConnectOpportunity = true;
                $campusConnectStory = true;
                if (in_array($request->integrate_campus_connect, ['job', 'both'])) {
                    $campusConnectOpportunity = $this->campusConnectOpportunityService->updateOrCreate(
                        data_get($createChallenge, 'id'),
                        data_get($createChallenge, 'slug', '-'),
                        Challenge::class,
                        $request->all(),
                        $organizationData,
                        auth()->user(),
                        $request->get('skills', [])
                    );
                }

                if (in_array($request->integrate_campus_connect, ['story', 'both'])) {
                    $campusConnectStory = $this->campusConnectStoryService->UpdateOrCreate(
                        data_get($createChallenge, 'id'),
                        data_get($createChallenge, 'slug', '-'),
                        Challenge::class,
                        $request->all(),
                        $organizationData,
                    );
                }

                if (!$createChallenge || !$updateChallengeDescription || !$createChallengeAchievement || !$createChallengeTypeMode || !$createChallengeSponsor || !$createChallengeJobs || !$createChallengeSkillsGroupsStack || !$createChallengeRequirement || !$createChallengeAssessmentCriteria || !$createChallengeAssessment || !$createChallengeProjectTemplate || !$createChallengeTimelines || !$createChallengeCustomTimelines || !$createChallengeExternalLink || !$createChallengeComponentAssociation || !$campusConnectOpportunity || !$campusConnectStory) {
                    throw new Exception('Failed to create challenge');
                }

                return [
                    'createChallenge'                     => $createChallenge,
                    'updateChallengeDescription'          => $updateChallengeDescription,
                    'createChallengeAchievement'          => $createChallengeAchievement,
                    'createChallengeTypeMode'             => $createChallengeTypeMode,
                    'createChallengeSponsor'              => $createChallengeSponsor,
                    'createChallengeJobs'                 => $createChallengeJobs,
                    'createChallengeSkillsGroupsStack'    => $createChallengeSkillsGroupsStack,
                    'createChallengeRequirement'          => $createChallengeRequirement,
                    'createChallengeAssessmentCriteria'   => $createChallengeAssessmentCriteria,
                    'createChallengeAssessment'           => $createChallengeAssessment,
                    'createChallengeProjectTemplate'      => $createChallengeProjectTemplate,
                    'createChallengeTimelines'            => $createChallengeTimelines,
                    'createChallengeCustomTimelines'      => $createChallengeCustomTimelines,
                    'createChallengeExternalLink'         => $createChallengeExternalLink,
                    'createChallengeComponentAssociation' => $createChallengeComponentAssociation,
                    'campusConnectOpportunity'            => $campusConnectOpportunity,
                    'campusConnectStory'                  => $campusConnectStory,
                ];
            });
            if (
                $createChallenge['createChallenge'] &&
                $createChallenge['updateChallengeDescription'] &&
                $createChallenge['createChallengeAchievement'] &&
                $createChallenge['createChallengeTypeMode'] &&
                $createChallenge['createChallengeSponsor'] &&
                $createChallenge['createChallengeJobs'] &&
                $createChallenge['createChallengeSkillsGroupsStack'] &&
                $createChallenge['createChallengeRequirement'] &&
                $createChallenge['createChallengeAssessmentCriteria'] &&
                $createChallenge['createChallengeAssessment'] &&
                $createChallenge['createChallengeProjectTemplate'] &&
                $createChallenge['createChallengeTimelines'] &&
                $createChallenge['createChallengeCustomTimelines'] &&
                $createChallenge['createChallengeExternalLink'] &&
                $createChallenge['createChallengeComponentAssociation'] &&
                $createChallenge['campusConnectOpportunity'] &&
                $createChallenge['campusConnectStory']
            ) {
                MixpenalJob::dispatch(
                    config('mixpanel.create_challenge'),
                    $request,
                    auth()->user(),
                    $request->ip()
                );
                return $createChallenge['createChallenge'];
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallenge in ChallengeRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function createChallengeUsingAIPreview($request)
    {
        try {
            $createChallengeUsingAIPreview = $this->aiService->createChallengeUsingAIPreview($request);

            return $createChallengeUsingAIPreview;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeUsingAIPreview in ChallengeRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function createChallengeFromResourceUsingAIPreview($request)
    {
        try {
            $createChallengeFromResourceUsingAIPreview = $this->aiService->createChallengeFromResourceUsingAIPreview($request);

            return $createChallengeFromResourceUsingAIPreview;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeFromResourceUsingAIPreview in ChallengeRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function createChallengeUsingAI($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment, $organization)
    {
        try {
            $createChallengeAssessmentUsingAi = $this->aiService->createChallengeAssessmentUsingAi($request);

            $updatedData = array_merge($request->json()->all(), $createChallengeAssessmentUsingAi);
            $request->json()->replace($updatedData);

            $createdChallenge = DB::transaction(function () use ($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment, $organization) {
                $createChallenge = $this->challengeService->createChallenge($request, $upload_cover_image, $organization->id);
                $createChallengeAchievement = $this->challengeAchievementService->createChallengeAchievement($request, $createChallenge->id, $upload_achievement_image);
                $createChallengeSkillsGroupsStack = $this->challengeSkillsGroupsStackService->createChallengeSkillsGroupsStack($request, $createChallenge->id);
                $createChallengeJobs = $this->challengeJobsService->createChallengeJobs($request, $createChallenge->id);
                $createChallengeRequirement = $this->challengeRequirementService->createChallengeRequirement($request, $createChallenge->id);
                $createChallengeProjectPitch = $this->projectPitchService->createChallengeAIProjectPitch($request);
                $createChallengeProjectTemplate = $this->challengeProjectTemplateService->createChallengeProjectTemplate($request, $createChallenge->id, $createChallengeProjectPitch);
                $createChallengeTimelines = $this->challengeTimelinesService->createChallengeTimelines($request, $createChallenge->id);
                $createChallengeAssociations = $this->componentAssociationService->createChallengeComponentAssociation($request, $createChallenge->id);
                $createChallengeAssessment = $this->challengeAssessmentService->createChallengeAssessment($request, $createChallenge->id, $upload_assessment_attachment);
                $createChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->createChallengeAssessmentCriteria($request, $createChallenge->id, $createChallengeAssessment);

                return [
                    'createChallenge'                       => $createChallenge,
                    'createChallengeAchievement'            => $createChallengeAchievement,
                    'createChallengeSkillsGroupsStack'      => $createChallengeSkillsGroupsStack,
                    'createChallengeRequirement'            => $createChallengeRequirement,
                    'createChallengeJobs'                   => $createChallengeJobs,
                    'createChallengeProjectPitch'           => $createChallengeProjectPitch,
                    'createChallengeProjectTemplate'        => $createChallengeProjectTemplate,
                    'createChallengeTimelines'              => $createChallengeTimelines,
                    'createChallengeAssociations'           => $createChallengeAssociations,
                    'createChallengeAssessment'             => $createChallengeAssessment,
                    'createChallengeAssessmentCriteria'     => $createChallengeAssessmentCriteria,
                ];
            });

            return $createdChallenge['createChallenge'];
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeUsingAI in ChallengeRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function uploadChallengeParticipationAchievementImage($image)
    {
        try {
            return $this->challengeAchievementService->uploadChallengeParticipationAchievementImage($image);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengeSponsor($request, $challenge)
    {
        try {
            return $this->challengeSponsorService->createChallengeSponsor($request, $challenge);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengeSkillsGroupsStack($request, $challenge)
    {
        try {
            return $this->challengeSkillsGroupsStackService->createChallengeSkillsGroupsStack($request, $challenge);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengeRequirement($request, $challenge)
    {
        try {
            return $this->challengeRequirementService->createChallengeRequirement($request, $challenge);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengeProjectTemplate($request, $challenge)
    {
        try {
            return $this->challengeProjectTemplateService->createChallengeProjectTemplate($request, $challenge);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateChallenge($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment, $organizationData)
    {
        try {
            $updateChallenge = DB::transaction(function () use ($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment, $organizationData) {
                $updateChallengeDescription = true;
                $updateChallenge = $this->challengeService->updateChallenge($slug, $request, $update_cover_image, $organizationData->id);
                if ($request->description_type == 'scorm' && $request->file('scorm_file')) {
                    $updateChallengeDescription = $this->scormRepository->upload(Challenge::class, $updateChallenge->id, $request->file('scorm_file'), $updateChallenge->scorm);
                }
                $updateChallengeTypeMode = $this->challengeTypeModeService->storeChallengeTypeMode($request, $updateChallenge->id);
                $updateChallengeSponsor = $this->challengeSponsorService->updateChallengeSponsor($updateChallenge->id, $request);
                $updateChallengeJobs = $this->challengeJobsService->updateChallengeJobs($request, $updateChallenge->id);
                $updateChallengeSkillsGroupsStack = $this->challengeSkillsGroupsStackService->updateChallengeSkillsGroupsStack($request, $updateChallenge->id);
                $updateChallengeTimelines = $this->challengeTimelinesService->updateChallengeTimelines($request, $updateChallenge->id);
                $updateChallengeCustomTimelines = $this->challengeCustomTimelinesService->updateChallengeCustomTimelines($request, $updateChallenge->id);
                $updateChallengeExternalLinks = $this->challengeExternalLinkService->updateChallengeExternalLink($request, $updateChallenge->id);
                $updateChallengeAssociation = $this->componentAssociationService->updateChallengeComponentAssociation($request, $updateChallenge->id);

                $updateChallengeAchievement = true;
                $updateChallengeRequirement = true;
                $updateChallengeAssessment = true;
                $updateChallengeAssessmentCriteria = true;
                $updateChallengeProjectTemplate = true;

                if (!Challenge::query()->whereHas('submitted_projects')->where('slug', $slug)->exists()) {
                    $updateChallengeAchievement = $this->challengeAchievementService->updateChallengeAchievement($updateChallenge->id, $request, $update_participation_achievement_image);
                    $updateChallengeRequirement = $this->challengeRequirementService->updateChallengeRequirement($request, $updateChallenge->id);
                    $updateChallengeAssessment = $this->challengeAssessmentService->updateChallengeAssessment($request, $updateChallenge->id, $update_assessment_attachment);
                    $updateChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->updateChallengeAssessmentCriteria($request, $updateChallenge->id, $updateChallengeAssessment);
                    $updateChallengeProjectTemplate = $this->challengeProjectTemplateService->updateChallengeProjectTemplate($request, $updateChallenge->id);
                }

                $campusConnectOpportunity = true;
                $campusConnectStory = true;
                if (in_array($request->integrate_campus_connect, ['job', 'both'])) {
                    $campusConnectOpportunity = $this->campusConnectOpportunityService->updateOrCreate(
                        data_get($updateChallenge, 'id'),
                        data_get($updateChallenge, 'slug', '-'),
                        Challenge::class,
                        $request->all(),
                        $organizationData,
                        auth()->user(),
                        $request->get('skills', [])
                    );
                }

                if (in_array($request->integrate_campus_connect, ['story', 'both'])) {
                    $campusConnectStory = $this->campusConnectStoryService->UpdateOrCreate(
                        data_get($updateChallenge, 'id'),
                        data_get($updateChallenge, 'slug', '-'),
                        Challenge::class,
                        $request->all(),
                        $organizationData,
                    );
                }

                if (!$updateChallenge || !$updateChallengeDescription || !$updateChallengeTypeMode || !$updateChallengeAchievement || !$updateChallengeSponsor || !$updateChallengeJobs || !$updateChallengeSkillsGroupsStack || !$updateChallengeRequirement || !$updateChallengeAssessmentCriteria || !$updateChallengeAssessment || !$updateChallengeProjectTemplate || !$updateChallengeTimelines || !$updateChallengeCustomTimelines || !$updateChallengeExternalLinks || !$updateChallengeAssociation || !$campusConnectOpportunity || !$campusConnectStory) {
                    throw new Exception('Failed to update challenge');
                }

                return [
                    'updateChallenge'                   => $updateChallenge,
                    'updateChallengeDescription'        => $updateChallengeDescription,
                    'updateChallengeTypeMode'           => $updateChallengeTypeMode,
                    'updateChallengeAchievement'        => $updateChallengeAchievement,
                    'updateChallengeSponsor'            => $updateChallengeSponsor,
                    'updateChallengeJobs'               => $updateChallengeJobs,
                    'updateChallengeSkillsGroupsStack'  => $updateChallengeSkillsGroupsStack,
                    'updateChallengeRequirement'        => $updateChallengeRequirement,
                    'updateChallengeAssessmentCriteria' => $updateChallengeAssessmentCriteria,
                    'updateChallengeAssessment'         => $updateChallengeAssessment,
                    'updateChallengeProjectTemplate'    => $updateChallengeProjectTemplate,
                    'updateChallengeTimelines'          => $updateChallengeTimelines,
                    'updateChallengeCustomTimelines'    => $updateChallengeCustomTimelines,
                    'updateChallengeExternalLinks'      => $updateChallengeExternalLinks,
                    'updateChallengeAssociation'        => $updateChallengeAssociation,
                    'campusConnectOpportunity'          => $campusConnectOpportunity,
                    'campusConnectStory'                => $campusConnectStory,
                ];
            });

            if (
                $updateChallenge['updateChallenge'] &&
                $updateChallenge['updateChallengeDescription'] &&
                $updateChallenge['updateChallengeTypeMode'] &&
                $updateChallenge['updateChallengeAchievement'] &&
                $updateChallenge['updateChallengeSponsor'] &&
                $updateChallenge['updateChallengeJobs'] &&
                $updateChallenge['updateChallengeSkillsGroupsStack'] &&
                $updateChallenge['updateChallengeRequirement'] &&
                $updateChallenge['updateChallengeAssessmentCriteria'] &&
                $updateChallenge['updateChallengeAssessment'] &&
                $updateChallenge['updateChallengeProjectTemplate'] &&
                $updateChallenge['updateChallengeTimelines'] &&
                $updateChallenge['updateChallengeCustomTimelines'] &&
                $updateChallenge['updateChallengeExternalLinks'] &&
                $updateChallenge['updateChallengeAssociation'] &&
                $updateChallenge['campusConnectOpportunity'] &&
                $updateChallenge['campusConnectStory']
            ) {
                MixpenalJob::dispatch(
                    config('mixpanel.edit_challenge'), 
                    $request, 
                    auth()->user(), 
                    $request->ip()
                );
                return $updateChallenge['updateChallenge'];
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeBasedOnSlug($slug)
    {
        try {
            return $this->challengeService->getChallengeBasedOnSlug($slug);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteChallenge($challenge_id, $request)
    {
        try {
            DB::beginTransaction();
            $challenge_data = ChallengeService::getChallengeBasedOnId($challenge_id);
            $deleteChallenge = $this->challengeService->deleteChallenge($challenge_id);
            $challenge_data->skills = $challenge_data->skills?->pluck('foreign_id');

            if ($deleteChallenge == false) {
                DB::rollBack();

                return false;
            }
            MixpenalJob::dispatch(config('mixpanel.delete_challenge'), $challenge_data, auth()->user(), $request->ip()
            );
            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return $this->challengeService->getChallengeBasedOnSlug($slug);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $labSlug = $this->challengeService->checkNameExistsOrNot($title);

            return $labSlug;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeAssessmentData($challengeAssessment)
    {
        try {
            return $this->challengeAssessmentService->getChallengeAssessmentData($challengeAssessment);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateChallengeAssessment($challengeId, $update_assessment_attachment, $request)
    {
        try {
            $updatedChallengeAssessment = DB::transaction(function () use ($challengeId, $update_assessment_attachment, $request) {
                $updateChallengeAssessment = $this->challengeAssessmentService->updateChallengeAssessment($request, $challengeId, $update_assessment_attachment);
                $updateChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->updateChallengeAssessmentCriteria($request, $challengeId, $updateChallengeAssessment);

                if (!$updateChallengeAssessmentCriteria || !$updateChallengeAssessment) {
                    throw new Exception('Failed to update challenge assessment');
                }

                return [
                    'updateChallengeAssessmentCriteria' => $updateChallengeAssessmentCriteria,
                    'updateChallengeAssessment'         => $updateChallengeAssessment,
                ];
            });

            if (
                $updatedChallengeAssessment['updateChallengeAssessmentCriteria'] &&
                $updatedChallengeAssessment['updateChallengeAssessment']
            ) {
                return $updatedChallengeAssessment;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneChallenge($challengeId, $organization)
    {
        try {
            $originalChallenge = Challenge::with(['skills', 'skill_groups', 'skill_stacks', 'participation_achievement', 'incentive_achievement', 'challenge_requirements', 'hosts', 'challenge_assessment_criteria', 'challenge_assessment', 'challenge_timelines', 'challenge_custom_timelines', 'challenge_project_template', 'external_links'])->find($challengeId);
            $cloneChallenge = DB::transaction(function () use ($challengeId, $organization, $originalChallenge) {
                $cloneChallenge = $this->challengeService->cloneChallenge($challengeId, $organization);
                $cloneChallengeParticipationAchievement = $this->challengeAchievementService->cloneChallengeParticipationAchievement($originalChallenge->participation_achievement, $cloneChallenge->id);
                $cloneChallengeIncentiveAchievement = $this->challengeAchievementService->cloneChallengeIncentiveAchievement($originalChallenge->incentive_achievement, $cloneChallenge->id);
                $cloneChallengeSkills = $this->challengeSkillsGroupsStackService->cloneChallengeSkills($originalChallenge->skills, $cloneChallenge->id);
                $cloneChallengeGroups = $this->challengeSkillsGroupsStackService->cloneChallengeGroups($originalChallenge->skill_groups, $cloneChallenge->id);
                $cloneChallengeStack = $this->challengeSkillsGroupsStackService->cloneChallengeStack($originalChallenge->skill_stacks, $cloneChallenge->id);
                $cloneChallengeSponsor = $this->challengeSponsorService->cloneChallengeSponsor($originalChallenge->hosts, $cloneChallenge->id);
                $cloneChallengeRequirement = $this->challengeRequirementService->cloneChallengeRequirement($originalChallenge->challenge_requirements, $cloneChallenge->id);
                $cloneChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->cloneChallengeAssessmentCriteria($originalChallenge->challenge_assessment_criteria, $cloneChallenge->id);
                $cloneChallengeAssessment = $this->challengeAssessmentService->cloneChallengeAssessment($originalChallenge->challenge_assessment, $cloneChallenge->id);
                $cloneChallengeProjectTemplate = $this->challengeProjectTemplateService->cloneChallengeProjectTemplate($originalChallenge->challenge_project_template, $cloneChallenge->id);
                $cloneChallengeTimelines = $this->challengeTimelinesService->cloneChallengeTimelines($originalChallenge->challenge_timelines, $cloneChallenge->id);
                $cloneChallengeCustomTimelines = $this->challengeCustomTimelinesService->cloneChallengeCustomTimelines($originalChallenge->challenge_custom_timelines, $cloneChallenge->id);
                $cloneChallengeExternalLink = $this->challengeExternalLinkService->cloneChallengeExternalLink($originalChallenge->external_links, $cloneChallenge->id);
                $cloneChallengeAssociaton = $this->componentAssociationService->cloneChallengeAssociaton($originalChallenge->challenge_association, $cloneChallenge->id);

                if (!$cloneChallenge || !$cloneChallengeParticipationAchievement || !$cloneChallengeIncentiveAchievement || !$cloneChallengeSkills || !$cloneChallengeGroups || !$cloneChallengeStack || !$cloneChallengeSponsor || !$cloneChallengeRequirement || !$cloneChallengeAssessmentCriteria || !$cloneChallengeAssessment || !$cloneChallengeProjectTemplate || !$cloneChallengeTimelines || !$cloneChallengeCustomTimelines || !$cloneChallengeExternalLink || !$cloneChallengeAssociaton) {
                    throw new Exception('Failed to clone challenge');
                }

                return [
                    'cloneChallenge'                         => $cloneChallenge,
                    'cloneChallengeParticipationAchievement' => $cloneChallengeParticipationAchievement,
                    'cloneChallengeIncentiveAchievement'     => $cloneChallengeIncentiveAchievement,
                    'cloneChallengeSkills'                   => $cloneChallengeSkills,
                    'cloneChallengeGroups'                   => $cloneChallengeGroups,
                    'cloneChallengeStack'                    => $cloneChallengeStack,
                    'cloneChallengeSponsor'                  => $cloneChallengeSponsor,
                    'cloneChallengeRequirement'              => $cloneChallengeRequirement,
                    'cloneChallengeAssessmentCriteria'       => $cloneChallengeAssessmentCriteria,
                    'cloneChallengeAssessment'               => $cloneChallengeAssessment,
                    'cloneChallengeProjectTemplate'          => $cloneChallengeProjectTemplate,
                    'cloneChallengeTimelines'                => $cloneChallengeTimelines,
                    'cloneChallengeCustomTimelines'          => $cloneChallengeCustomTimelines,
                    'cloneChallengeExternalLink'             => $cloneChallengeExternalLink,
                    'cloneChallengeAssociaton'               => $cloneChallengeAssociaton,
                ];
            });

            if (
                $cloneChallenge['cloneChallenge'] &&
                $cloneChallenge['cloneChallengeParticipationAchievement'] &&
                $cloneChallenge['cloneChallengeIncentiveAchievement'] &&
                $cloneChallenge['cloneChallengeSkills'] &&
                $cloneChallenge['cloneChallengeGroups'] &&
                $cloneChallenge['cloneChallengeStack'] &&
                $cloneChallenge['cloneChallengeSponsor'] &&
                $cloneChallenge['cloneChallengeRequirement'] &&
                $cloneChallenge['cloneChallengeAssessmentCriteria'] &&
                $cloneChallenge['cloneChallengeAssessment'] &&
                $cloneChallenge['cloneChallengeProjectTemplate'] &&
                $cloneChallenge['cloneChallengeTimelines'] &&
                $cloneChallenge['cloneChallengeCustomTimelines'] &&
                $cloneChallenge['cloneChallengeExternalLink'] &&
                $cloneChallenge['cloneChallengeAssociaton']
            ) {
                return $cloneChallenge['cloneChallenge'];
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengeAnnouncement($challengeId, $request)
    {
        try {
            $createAnnouncement = DB::transaction(function () use ($challengeId, $request) {
                $createAnnouncement = $this->challengeAnnouncementService->createChallengeAnnouncement($challengeId, $request);

                if (!$createAnnouncement) {
                    throw new Exception('Failed to create announcement');
                }

                return [
                    'createAnnouncement' => $createAnnouncement,
                ];
            });

            return $createAnnouncement['createAnnouncement'];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteChallengeAnnouncement($challengeAnnouncementId)
    {
        try {
            DB::beginTransaction();

            $deleteChallengeAnnouncement = $this->challengeAnnouncementService->deleteChallengeAnnouncement($challengeAnnouncementId);
            if ($deleteChallengeAnnouncement == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public function getChallengeListName($request, $organization)
    {
        try {
            return $this->challengeService->getChallengeListName($request, $organization);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function selectChallengeWinner($challengeData, $request)
    {
        try {
            $submitProject = DB::transaction(function () use ($challengeData, $request) {
                $updateWinnerSelectionTimeLine = true;
                $addWinnerAchievement = $this->achievementService->addWinnerAchievement($challengeData, $request);
                if ($challengeData->winner_select_date == null) {
                    $updateWinnerSelectionTimeLine = $this->challengeService->updateWinnerSelectionTimeLine($challengeData);
                }

                if (!$addWinnerAchievement || !$updateWinnerSelectionTimeLine) {
                    throw new Exception('Failed to select challenge winners');
                }

                return [
                    'addWinnerAchievement'          => $addWinnerAchievement,
                    'updateWinnerSelectionTimeLine' => $updateWinnerSelectionTimeLine,
                ];
            });

            if (
                $submitProject['addWinnerAchievement'] &&
                $submitProject['updateWinnerSelectionTimeLine']
            ) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectIdsBasedOnChallenge($challengeId)
    {
        try {
            return $this->projectService->fetchProjectIdsBasedOnChallenge($challengeId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchProjectIds($projectIds, $request)
    {
        try {
            return $this->projectService->getProjectList($projectIds, $request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchAssessedProjectBasedOnChallenge($challengeId)
    {
        try {
            return $this->projectService->fetchAssessedProjectBasedOnChallenge($challengeId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function incrementView(Challenge $challenge)
    {
        try {
            return $this->challengeService->incrementView($challenge);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
