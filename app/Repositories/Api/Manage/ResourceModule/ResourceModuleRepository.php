<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use App\Helpers\MixpanelHelper;
use App\Services\Manage\AIService;
use App\Services\Manage\ResourceModuleDetailService;
use App\Services\Manage\ResourceModuleRatingService;
use App\Services\Manage\ResourceModuleService;
use App\Services\Manage\ResourceModuleSkillsGroupsStackService;
use App\Services\Manage\ResourceModuleTagsGroupsService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;

    protected $resourceModuleDetailsService;

    protected $resouceModuleSkillsGroupStackService;
    protected $resourceModuleRatingService;

    protected $resourceModuleTagsGroupsService;
    private $aiService;

    public function __construct(ResourceModuleService $resourceModuleService, ResourceModuleDetailService $resourceModuleDetailsService, ResourceModuleSkillsGroupsStackService $resouceModuleSkillsGroupStackService, ResourceModuleRatingService $resourceModuleRatingService, ResourceModuleTagsGroupsService $resourceModuleTagsGroupsService, AIService $aiService)
    {
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceModuleDetailsService = $resourceModuleDetailsService;
        $this->resouceModuleSkillsGroupStackService = $resouceModuleSkillsGroupStackService;
        $this->resourceModuleTagsGroupsService = $resourceModuleTagsGroupsService;
        $this->resourceModuleRatingService = $resourceModuleRatingService;
        $this->aiService = $aiService;
    }

    public function getResourceModuleCountBasedOnOrganization($organizationId)
    {
        try {
            return $this->resourceModuleService->getResourceModuleCountBasedOnOrganization($organizationId);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getResourceModuleList($request, $organization)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleList($request, $organization);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createResourceModule($request, $upload_cover_image, $organizationId)
    {
        try {
            $createLabProgram = DB::transaction(function () use ($request, $upload_cover_image, $organizationId) {
                $createResourceModule = $this->resourceModuleService->createResourceModule($request, $upload_cover_image, $organizationId);
                $resourceModuleSkillsGroupStackService = $this->resouceModuleSkillsGroupStackService->createResourceModuleSkillsGroupsStack($request, $createResourceModule->id);
                $resourceModuleTagsGroupsService = $this->resourceModuleTagsGroupsService->createResourceModuleTagsGroups($request, $createResourceModule->id);

                return [
                    'createResourceModule'                  => $createResourceModule,
                    'resourceModuleSkillsGroupStackService' => $resourceModuleSkillsGroupStackService,
                    'resourceModuleTagsGroupsService'       => $resourceModuleTagsGroupsService,
                ];
            });
            $request->organization_id = $createLabProgram['createResourceModule']['organization_id'];
            if ($createLabProgram['createResourceModule'] && $createLabProgram['resourceModuleSkillsGroupStackService'] && $createLabProgram['resourceModuleTagsGroupsService']) {
                MixpanelHelper::mixpanel_tracking(config('mixpanel.create_resource'), $request, auth()->user(), $request->ip());
                DB::commit();

                return $createLabProgram['createResourceModule'];
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createResourceModuleUsingAI($request, $upload_cover_image)
    {
        try {
            $createdResourceModule = DB::transaction(function () use ($request, $upload_cover_image) {
                $createResourceModuleUsingAI = $this->resourceModuleService->createResourceModule($request, $upload_cover_image);
                $resourceModuleSkillsGroupStackService = $this->resouceModuleSkillsGroupStackService->createResourceModuleSkillsGroupsStack($request, $createResourceModuleUsingAI->id);

                return [
                    'createResourceModuleUsingAI'           => $createResourceModuleUsingAI,
                    'resourceModuleSkillsGroupStackService' => $resourceModuleSkillsGroupStackService,
                ];
            });

            return $createdResourceModule['createResourceModuleUsingAI'];
        } catch (Exception $e) {
            Log::error('Error in CreateResourceModuleUsingAI in ResourceModuleRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function createResourceModuleDetailsAI($request, $resource_module_id)
    {
        try {
            $createResourceModuleUsingAI = $this->resourceModuleDetailsService->createResourceModuleDetailsAI($request, $resource_module_id);

            return $createResourceModuleUsingAI;
        } catch (Exception $e) {
            Log::error('Error in createResourceModuleDetailsAI in ResourceModuleRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function createResourceModuleUsingAIPreview($request)
    {
        try {
            $createResourceModuleUsingAIPreview = $this->aiService->createResourceModuleUsingAIPreview($request);

            return $createResourceModuleUsingAIPreview;
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadResourceModuleCoverImage($cover_image)
    {
        try {
            return $this->resourceModuleService->uploadResourceModuleCoverImage($cover_image);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteResourceModule($slug, $resource_module_id, $request)
    {
        try {
            DB::beginTransaction();
            $getResouceModule = $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
            $getResouceModule->skills = $getResouceModule->skills->pluck('foreign_id')->unique();
            $deleteResourceModule = $this->resourceModuleService->deleteResourceModule($resource_module_id);
            if ($deleteResourceModule == false) {
                DB::rollBack();

                return false;
            }
            MixpanelHelper::mixpanel_tracking(config('mixpanel.delete_resource'), $getResouceModule, auth()->user(), $request->ip());
            DB::commit();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkName($title)
    {
        try {
            return $this->resourceModuleService->checkName($title);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateResourceModule($slug, $request, $upload_cover_image, $organizationId)
    {
        try {
            $updateResourceModule = DB::transaction(function () use ($slug, $request, $upload_cover_image, $organizationId) {
                $updateResourceModule = $this->resourceModuleService->updateResourceModule($slug, $request, $upload_cover_image, $organizationId);
                $resourceModuleSkillsGroupStackService = $this->resouceModuleSkillsGroupStackService->updateResourceModuleSkillsGroupsStack($request, $updateResourceModule->id);
                $resourceModuleTagsGroupsService = $this->resourceModuleTagsGroupsService->updateResourceModuleTagsGroups($request, $updateResourceModule->id);

                return [
                    'updateResourceModule'            => $updateResourceModule,
                    'resourceModuleSkillsGroupsStack' => $resourceModuleSkillsGroupStackService,
                    'resourceModuleTagsGroupsService' => $resourceModuleTagsGroupsService,
                ];
            });
            $request->organization_id = $updateResourceModule['updateResourceModule']['organization_id'];
            if ($updateResourceModule['updateResourceModule'] && $updateResourceModule['resourceModuleSkillsGroupsStack'] && $updateResourceModule['resourceModuleTagsGroupsService']) {
                MixpanelHelper::mixpanel_tracking(config('mixpanel.edit_resource'), $request, auth()->user(), $request->ip());
                DB::commit();

                return $updateResourceModule['updateResourceModule'];
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fileUpload($request, $resource_module_id)
    {
        try {
            return $this->resourceModuleDetailsService->fileUpload($request, $resource_module_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteResourceModuleMedia($request, $resource_module_id)
    {
        try {
            return $this->resourceModuleDetailsService->deleteResourceModuleMedia($request, $resource_module_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addLinks($request, $resource_module_id)
    {
        try {
            return $this->resourceModuleDetailsService->addLinks($request, $resource_module_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addEmbeddedMedia($request, $resource_module_id)
    {
        try {
            return $this->resourceModuleDetailsService->addEmbeddedMedia($request, $resource_module_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getListName($request, $organization)
    {
        try {
            return  $this->resourceModuleService->getListName($request, $organization);
        } catch (\Exception $e) {
            return false;
        }
    }
}
