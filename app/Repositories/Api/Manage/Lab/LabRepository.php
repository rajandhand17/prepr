<?php

namespace App\Repositories\Api\Manage\Lab;

use App\Helpers\MixpanelHelper;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Services\DurationService;
use App\Services\FeaturedLabService;
use App\Services\Manage\AirmeetEventService;
use App\Services\Manage\AIService;
use App\Services\Manage\CampusConnectOpportunityService;
use App\Services\Manage\CampusConnectStoryService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabAcheivementService;
use App\Services\Manage\LabAddressService;
use App\Services\Manage\LabExternalLinksService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;
use App\Services\Manage\LabTypeModesService;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\OrganizationService;
use App\Services\SkillService;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;

class LabRepository implements LabInterface
{
    private $labService;
    private $memberManagementService;
    private $labAddressService;
    private $labExternalLinksService;
    private $labSkillsGroupsStackService;
    private $labTagsGroupsService;
    private $labAcheivementService;
    private $skillService;
    private $componentAssociationService;
    private $durationService;
    private $aiService;
    private $airmeetEventService;

    private $campusConnectOpportunityService;
    private $campusConnectStoryService;
    private $organizationService;
    private $labTypeModesService;
    private $featuredLabService;

    public function __construct(FeaturedLabService $featuredLabService, LabService $labService, MemberManagementService $memberManagementService, LabAddressService $labAddressService, LabExternalLinksService $labExternalLinksService, LabSkillsGroupsStackService $labSkillsGroupsStackService, LabTagsGroupsService $labTagsGroupsService, LabAcheivementService $labAcheivementService, SkillService $skillService, ComponentAssociationService $componentAssociationService, DurationService $durationService, AIService $aiService, CampusConnectOpportunityService $campusConnectOpportunityService, CampusConnectStoryService $campusConnectStoryService, OrganizationService $organizationService, AirmeetEventService $airmeetEventService, LabTypeModesService $labTypeModesService)
    {
        $this->labService = $labService;
        $this->memberManagementService = $memberManagementService;
        $this->labAddressService = $labAddressService;
        $this->labExternalLinksService = $labExternalLinksService;
        $this->labSkillsGroupsStackService = $labSkillsGroupsStackService;
        $this->labTagsGroupsService = $labTagsGroupsService;
        $this->labAcheivementService = $labAcheivementService;
        $this->skillService = $skillService;
        $this->componentAssociationService = $componentAssociationService;
        $this->durationService = $durationService;
        $this->aiService = $aiService;
        $this->airmeetEventService = $airmeetEventService;
        $this->campusConnectOpportunityService = $campusConnectOpportunityService;
        $this->campusConnectStoryService = $campusConnectStoryService;
        $this->organizationService = $organizationService;
        $this->labTypeModesService = $labTypeModesService;
        $this->featuredLabService = $featuredLabService;
    }

    public function getLabCountBasedOnOrganization($organizationId)
    {
        try {
            return $this->labService->getLabCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabList($request, $organization)
    {
        try {
            return $this->labService->getLabList($request, $organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
    public function cloneLab($slug)
    {
        try {
            $getLab=$this->labService->getLabBasedOnSlugWithRelations($slug);
            if($getLab){
                $cloneLab=$this->labService->cloneLab($getLab);
            }
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
    public function uploadLabCoverImage($image)
    {
        try {
            return $this->labService->uploadLabCoverImage($image);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createLab($request, $upload_profile_image, $upload_achievements_image, $organizationData)
    {
        try {
            $createdLab = DB::transaction(function () use ($request, $upload_profile_image, $upload_achievements_image, $organizationData) {
                $createLab = $this->labService->createLab($request, $upload_profile_image, $organizationData->id);
                $createdLabAddress = $this->labAddressService->createLabAddress($request, $createLab);
                $createdLabSkillAssociations = $this->labSkillsGroupsStackService->createLabSkillsGroupsStack($request, $createLab);
                $createdLabExternalLinks = $this->labExternalLinksService->createLabExternalLinks($request, $createLab);
                $labTypeModes = $this->labTypeModesService->labTypeModes($request, $createLab->id);
                if ($request->is_achievement_enabled == 'yes') {
                    $createdLabAchievement = $this->labAcheivementService->createLabAchievement($request, $createLab, $upload_achievements_image);
                }
                $createdLabAssociations = $this->componentAssociationService->labAssociation($request, $createLab);
                /** LIVE EVENT */
                if ($request->get('is_live_event_enabled') === 'yes') {
                    $createdEvent = $this->airmeetEventService->createUpdateEvent(
                        Lab::class,
                        $createLab->id,
                        [
                            'live_event_url' => $request->validated('live_event.url'),
                        ]
                    );
                }
                $campusConnectOpportunity = true;
                $campusConnectStory = true;
                if (in_array($request->integrate_campus_connect, ['job', 'both'])) {
                    $campusConnectOpportunity = $this->campusConnectOpportunityService->updateOrCreate(
                        data_get($createLab, 'id'),
                        data_get($createLab, 'slug', '-'),
                        Lab::class,
                        $request->all(),
                        $organizationData,
                        auth()->user(),
                        $request->get('skills', [])
                    );
                }

                if (in_array($request->integrate_campus_connect, ['story', 'both'])) {
                    $campusConnectStory = $this->campusConnectStoryService->UpdateOrCreate(
                        data_get($createLab, 'id'),
                        data_get($createLab, 'slug', '-'),
                        Lab::class,
                        $request->all(),
                        $organizationData,
                    );
                }

                return [
                    'createdLab'                  => $createLab,
                    'createdLabAddress'           => $createdLabAddress,
                    'createdLabSkillAssociations' => $createdLabSkillAssociations,
                    'createdLabExternalLinks'     => $createdLabExternalLinks,
                    'createdLabAchievement'       => ($request->is_achievement_enabled == 'yes') ? $createdLabAchievement : true,
                    'createdLabAssociations'      => $createdLabAssociations,
                    'createdEvent'                => $request->is_live_event_enabled == 'yes' ? $createdEvent : true,
                    'campusConnectOpportunity'    => $campusConnectOpportunity,
                    'campusConnectStory'          => $campusConnectStory,
                    'labTypeModes'                => $labTypeModes,
                ];
            });
            if (
                $createdLab['createdLab'] &&
                $createdLab['createdLabAddress'] &&
                $createdLab['createdLabSkillAssociations'] &&
                $createdLab['createdLabExternalLinks'] &&
                $createdLab['createdLabAchievement'] &&
                $createdLab['createdLabAssociations'] &&
                $createdLab['createdEvent'] &&
                $createdLab['campusConnectOpportunity'] &&
                $createdLab['campusConnectStory'] &&
                $createdLab['labTypeModes']
            ) {
                DB::commit();
                $groups_for_mixpanel = [];
                if ($request->has('lab_programs') && !empty($request->lab_programs)) {
                    $groups_for_mixpanel = LabProgramService::getLabProgramTitleBasedOnUUIDArray($request->lab_programs);
                }
                MixpanelHelper::mixpanel_tracking(
                    config('mixpanel.create_lab'),
                    $request,
                    auth()->user(),
                    $request->ip(),
                    $groups_for_mixpanel
                );

                return $createdLab['createdLab'];
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public function updateLab($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationData)
    {
        try {
            $updatedLab = DB::transaction(function () use ($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationData) {
                $updateLab = $this->labService->updateLab($slug, $request, $upload_cover_image, $organizationData);
                $updatedLabAddress = $this->labAddressService->updateLabAddress($request, $updateLab->id);
                $updatedLabSkillAssociations = $this->labSkillsGroupsStackService->updateLabSkillsGroupsStack($request, $updateLab->id);

                $updatedLabExternalLinks = $this->labExternalLinksService->updateLabExternalLinks($request, $updateLab->id);
                $labTypeModes = $this->labTypeModesService->labTypeModes($request, $updateLab->id);
                if ($request->is_achievement_enabled == 'yes') {
                    $updatedLabAchievement = $this->labAcheivementService->updateLabAchievement($request, $updateLab->id, $upload_achievement_image);
                }
                $updatedLabAssociations = $this->componentAssociationService->updateLabAssociation($request, $updateLab->id);
                /** LIVE EVENT */
                if ($request->get('is_live_event_enabled') === 'yes') {
                    $updatedEvent = $this->airmeetEventService->createUpdateEvent(
                        Lab::class,
                        $updateLab->id,
                        [
                            'live_event_url' => $request->validated('live_event.url'),
                        ]
                    );
                }
                $campusConnectOpportunity = true;
                $campusConnectStory = true;
                if (in_array($request->integrate_campus_connect, ['job', 'both'])) {
                    $campusConnectOpportunity = $this->campusConnectOpportunityService->updateOrCreate(
                        data_get($updateLab, 'id'),
                        data_get($updateLab, 'slug', '-'),
                        Lab::class,
                        $request->all(),
                        $organizationData,
                        auth()->user(),
                        $request->get('skills', [])
                    );
                }

                if (in_array($request->integrate_campus_connect, ['story', 'both'])) {
                    $campusConnectStory = $this->campusConnectStoryService->UpdateOrCreate(
                        data_get($updateLab, 'id'),
                        data_get($updateLab, 'slug', '-'),
                        Lab::class,
                        $request->all(),
                        $organizationData,
                    );
                }

                return [
                    'updatedLab'                  => $updateLab,
                    'updatedLabAddress'           => $updatedLabAddress,
                    'updatedLabSkillAssociations' => $updatedLabSkillAssociations,
                    'updatedLabExternalLinks'     => $updatedLabExternalLinks,
                    'updatedLabAchievement'       => ($request->is_achievement_enabled == 'yes') ? $updatedLabAchievement : true,
                    'updatedLabAssociations'      => $updatedLabAssociations,
                    'updatedEvent'                => $request->is_live_event_enabled == 'yes' ? $updatedEvent : true,
                    'campusConnectOpportunity'    => $campusConnectOpportunity,
                    'campusConnectStory'          => $campusConnectStory,
                    'labTypeModes'                => $labTypeModes,
                ];
            });
            if (
                $updatedLab['updatedLab'] &&
                $updatedLab['updatedLabAddress'] &&
                $updatedLab['updatedLabSkillAssociations'] &&
                $updatedLab['updatedLabExternalLinks'] &&
                $updatedLab['updatedLabAchievement'] &&
                $updatedLab['updatedLabAssociations'] &&
                $updatedLab['campusConnectOpportunity'] &&
                $updatedLab['campusConnectStory'] &&
                $updatedLab['labTypeModes']
            ) {
                DB::commit();
                $groups_for_mixpanel = [];
                if ($request->has('lab_programs') && !empty($request->lab_programs)) {
                    $groups_for_mixpanel = LabProgramService::getLabProgramTitleBasedOnUUIDArray($request->lab_programs);
                }
                MixpanelHelper::mixpanel_tracking(
                    config('mixpanel.edit_lab'),
                    $request,
                    auth()->user(),
                    $request->ip(),
                    $groups_for_mixpanel
                );

                return $updatedLab['updatedLab'];
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteLab($lab_id, $request)
    {
        try {
            DB::beginTransaction();
            $lab = $this->labService->getLabBasedOnId($lab_id);
            $deleteLab = $this->labService->deleteLab($lab_id);
            if ($deleteLab == false) {
                DB::rollBack();

                return false;
            }
            $lab->organization_id = OrganizationService::getOrganizationExistBasedOnId($lab->id);
            // Mixpanel tracking code: delete lab
            $lab->skills = LabSkillsGroupsStackService::getSkillsBasedOnLabId($lab->id);
            $lab->tags = LabTagsGroupsService::getTagIdBasedOnLabId($lab->id);
            MixpanelHelper::mixpanel_tracking(config('mixpanel.delete_lab'), $lab, auth()->user(), $request->ip());
            DB::commit();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $labSlug = $this->labService->checkNameExistsOrNot($title);

            return $labSlug;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabListName($request, $organization)
    {
        try {
            return $this->labService->getLabListName($request, $organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createLabUsingAIPreview($request)
    {
        try {
            $createLabUsingAIPreview = $this->aiService->createLabUsingAIPreview($request);

            return $createLabUsingAIPreview;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createLabUsingAIPreview in LabRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function createLabUsingAI($request, $upload_profile_image, $upload_achievements_image, $organization)
    {
        try {
            $createdLabUsingAI = DB::transaction(function () use ($request, $upload_profile_image, $organization) {
                $createLabUsingAI = $this->labService->createLabUsingAI($request, $upload_profile_image, $organization);
                $createdLabSkillAssociations = $this->labSkillsGroupsStackService->createLabSkillsGroupsStack($request, $createLabUsingAI);
                $createdLabAssociations = $this->componentAssociationService->labAssociation($request, $createLabUsingAI);

                return [
                    'createdLabUsingAI'           => $createLabUsingAI,
                    'createdLabSkillAssociations' => $createdLabSkillAssociations,
                    'createdLabAssociations'      => $createdLabAssociations,
                ];
            });

            return $createdLabUsingAI['createdLabUsingAI'];

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createLabUsingAI in LabRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function getFeaturedLabBasedOnId($id)
    {
        try {
            return $this->featuredLabService->getFeaturedLabBasedOnLabId($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createFeaturedLab($lab)
    {
        try {
            return $this->featuredLabService->createFeaturedLab($lab);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createLabUsingAI in LabRepository.php: '.$e->getMessage());

            return false;
        }
    }
}
