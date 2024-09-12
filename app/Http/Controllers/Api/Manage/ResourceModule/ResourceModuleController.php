<?php

namespace App\Http\Controllers\Api\Manage\ResourceModule;

use App\Helpers\ChargebeeHelper;
use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceModule\AddLinksResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\CreateResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\CreateResourceModuleUsingAIPreviewRequest;
use App\Http\Requests\Manage\ResourceModule\CreateResourceModuleUsingAIRequest;
use App\Http\Requests\Manage\ResourceModule\DeleteMediaResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\FileUploadResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\UpdateResourceModuleRequest;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleListNameResource;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleResource;
use App\Jobs\MixpanelJob;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use App\Services\LastVisitedActivityModuleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourceModuleController extends AppBaseController
{
    private $resourceModuleRepository;

    public function __construct(ResourceModuleRepository $resourceModuleRepository)
    {
        $this->resourceModuleRepository = $resourceModuleRepository;
    }

    public function index(Request $request)
    {
        try {
            if (!auth()->user()->isAbleTo('view_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $responseModuleList = $this->resourceModuleRepository->getResourceModuleList($request, $organization);
            if ($responseModuleList) {
                $response = [
                    'total_count'  => $responseModuleList->total(),
                    'per_page'     => $responseModuleList->perPage(),
                    'count'        => $responseModuleList->count(),
                    'current_page' => $responseModuleList->currentPage(),
                    'total_pages'  => $responseModuleList->lastPage(),
                    'list'         => ResourceModuleResource::collection($responseModuleList),
                ];

                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_list'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleExistsOrNot) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                if (!$organization) {
                    return $this->sendError(__('responses.selected_organization_not_found'), 404);
                }
                if ($checkResourceModuleExistsOrNot->organization_id != $organization->id) {
                    return $this->sendError(__('responses.resource_collection_switcher_error'), 403);
                }
                if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                    return $this->sendError(__('responses.resource_module_not_accessible'), 403);
                }
                $userId = auth()->user()->id;
                // For user progress tracking
                TrackUserProgressHelper::trackResourceModuleUserProgress($checkResourceModuleExistsOrNot, $userId);

                // For last visited activity tracking
                $moduleType = config('constants.module_type.resource_modules');
                LastVisitedActivityModuleService::lastVisitedActivityModule($checkResourceModuleExistsOrNot->id, $userId, $moduleType);
                MixpanelJob::dispatch(config('mixpanel.view_resource'), $checkResourceModuleExistsOrNot, auth()->user(), request()->ip());

                return $this->sendResponse(ResourceModuleResource::make($checkResourceModuleExistsOrNot), __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_view'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateResourceModuleRequest $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            // checks creation limits of the Resource Module
            $checkResourceModuleLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'resourceModule');
            if ($checkResourceModuleLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkResourceModuleCount = $this->resourceModuleRepository->getResourceModuleCountBasedOnOrganization($checkResourceModuleLimit['organizationId']);
                if ($checkResourceModuleLimit['fetchOrganizationPlanDetails'] <= $checkResourceModuleCount) {
                    return $this->sendError(__('responses.reached_resource_module_limit'), 400);
                }
            }

            $upload_cover_image = config('site-settings.default_resource_module_cover_image');
            if ($request->cover_image !== null) {
                if ($request->media_type == 'image') {
                    $uploaded_cover_image = $this->resourceModuleRepository->uploadResourceModuleCoverImage($request->cover_image);
                    if (!$uploaded_cover_image) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_image = $request->cover_image;
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $createResourceModule = $this->resourceModuleRepository->createResourceModule($request, $upload_cover_image, $organization->id);

            if ($createResourceModule) {
                return $this->sendResponse(ResourceModuleResource::make($createResourceModule), __('responses.resource_module_stored_success'), 200);
            }

            return $this->sendError(__('responses.resource_module_stored_failed'), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update(UpdateResourceModuleRequest $request, $slug)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if (!$checkResourceModuleExistsOrNot) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkResourceModuleExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.resource_collection_switcher_error'), 403);
            }
            if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }
            $upload_cover_image = str_replace(config('site-settings.aws_url'), '', $checkResourceModuleExistsOrNot->media);

            if ($request->cover_image !== null) {
                if ($request->media_type == 'image') {
                    $uploaded_cover_image = $this->resourceModuleRepository->uploadResourceModuleCoverImage($request->cover_image);
                    if (!$uploaded_cover_image) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_image = $request->cover_image;
                }
                $upload_cover_image = $uploaded_cover_image;
            }

            $updateResourceModule = $this->resourceModuleRepository->updateResourceModule($slug, $request, $upload_cover_image, $organization->id);
            if ($updateResourceModule) {
                return $this->sendResponse(ResourceModuleResource::make($updateResourceModule), __('responses.resource_module_update_success'), 200);
            }

            return $this->sendError(__('responses.resource_module_stored_failed'), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            if (!auth()->user()->isAbleTo('create_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $checkResourceModuleNameExistsOrNot = $this->resourceModuleRepository->checkName($title);
            if ($checkResourceModuleNameExistsOrNot) {
                return $this->sendError(__('responses.resource_module_name_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_module_name_available'), 200);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            if (!auth()->user()->isAbleTo('create_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $checkResourceModuleNameExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleNameExistsOrNot) {
                return $this->sendError(__('responses.resource_module_slug_not_available'));
            }

            return $this->sendResponse([], __('responses.resource_module_slug_available'), 200);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addLinksAndEmbedMedia(AddLinksResourceModuleRequest $request, $slug)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if (!$checkResourceModuleExistsOrNot) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkResourceModuleExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.resource_collection_switcher_error'), 403);
            }
            if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }
            if ($request->has('links') && !empty($request->links)) {
                $addLinks = $this->resourceModuleRepository->addLinks($request, $checkResourceModuleExistsOrNot->id);
                if (!$addLinks) {
                    return $this->sendError(__('responses.add_links_failed'), 403);
                }
            }
            if ($request->has('embed_media') && !empty($request->embed_media)) {
                $addEmbeddedMedia = $this->resourceModuleRepository->addEmbeddedMedia($request, $checkResourceModuleExistsOrNot->id);
                if (!$addEmbeddedMedia) {
                    return $this->sendError(__('responses.add_embedded_media_failed'), 403);
                }
            }

            return $this->sendResponse(ResourceModuleResource::make($checkResourceModuleExistsOrNot), __('responses.add_links_success'), 200);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fileUpload(FileUploadResourceModuleRequest $request, $slug)
    {
        try {
            $type = config('constants.resource_module_type.image');
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if (!$checkResourceModuleExistsOrNot) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkResourceModuleExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.resource_collection_switcher_error'), 403);
            }
            if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }
            $insertData = $this->resourceModuleRepository->fileUpload($request, $checkResourceModuleExistsOrNot->id);
            if ($insertData) {
                return $this->sendResponse(ResourceModuleResource::make($checkResourceModuleExistsOrNot), __('responses.file_upload_success'), 200);
            }

            return $this->sendError(__('responses.file_upload_failed'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if (!$checkResourceModuleExistsOrNot) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkResourceModuleExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.resource_collection_switcher_error'), 403);
            }
            if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }
            $deleteResourceModule = $this->resourceModuleRepository->deleteResourceModule($slug, $checkResourceModuleExistsOrNot->id);
            if ($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.resource_module_delete'));
            }

            return $this->sendError(__('responses.resource_module_not_delete'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteMedia(DeleteMediaResourceModuleRequest $request, $slug)
    {
        try {
            $checkResourceModuleExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if (!$checkResourceModuleExistsOrNot) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkResourceModuleExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.resource_collection_switcher_error'), 403);
            }
            if ($checkResourceModuleExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }
            $deleteResourceModule = $this->resourceModuleRepository->deleteResourceModuleMedia($request, $checkResourceModuleExistsOrNot->id);
            if ($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.resource_module_media_delete'));
            }

            return $this->sendError(__('responses.resource_module_media_not_delete'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getList(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $responseModuleList = $this->resourceModuleRepository->getListName($request, $organization);
            if ($responseModuleList) {
                return $this->sendResponse(ResourceModuleListNameResource::collection($responseModuleList), __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_view'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function CreateResourceModuleUsingAIPreview(CreateResourceModuleUsingAIPreviewRequest $request)
    {
        try {
            // checks creation limits of the Resource Module
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            // checks creation limits of the Resource Module
            $checkResourceModuleLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'resourceModule');
            if ($checkResourceModuleLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkResourceModuleCount = $this->resourceModuleRepository->getResourceModuleCountBasedOnOrganization($checkResourceModuleLimit['organizationId']);
                if ($checkResourceModuleLimit['fetchOrganizationPlanDetails'] <= $checkResourceModuleCount) {
                    return $this->sendError(__('responses.reached_resource_module_limit'), 400);
                }
            }

            $createResourceModuleUsingAIPreview = $this->resourceModuleRepository->createResourceModuleUsingAIPreview($request);
            if ($createResourceModuleUsingAIPreview) {
                return $this->sendResponse($createResourceModuleUsingAIPreview, __('responses.resource_modules_previews_created_successfully'), 200);
            } else {
                return $this->sendResponse([], '', 200);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in CreateResourceModuleUsingAIPreview in ResourceModuleController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }

    public function CreateResourceModuleUsingAI(CreateResourceModuleUsingAIRequest $request)
    {
        try {
            // checks creation limits of the Resource Module
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            // checks creation limits of the Resource Module
            $checkResourceModuleLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'resourceModule');
            if ($checkResourceModuleLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkResourceModuleCount = $this->resourceModuleRepository->getResourceModuleCountBasedOnOrganization($checkResourceModuleLimit['organizationId']);
                if ($checkResourceModuleLimit['fetchOrganizationPlanDetails'] <= $checkResourceModuleCount) {
                    return $this->sendError(__('responses.reached_resource_module_limit'), 400);
                }
            }

            $upload_cover_image = config('site-settings.default_resource_module_cover_image');
            $createResourceModuleUsingAI = $this->resourceModuleRepository->createResourceModuleUsingAI($request, $upload_cover_image, $organization->id);

            $createResourceModuleDetailsAI = $this->resourceModuleRepository->createResourceModuleDetailsAI($request, $createResourceModuleUsingAI->id);

            if (!$createResourceModuleDetailsAI) {
                throw new Exception('createResourceModuleDetailsAI has no value!');
            }

            if ($createResourceModuleUsingAI) {
                return $this->sendResponse(ResourceModuleResource::make($createResourceModuleUsingAI), __('responses.resource_module_created_successfully'), 200);
            } else {
                throw new Exception('CreateResourceModuleUsingAI has no value!');
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in CreateResourceModuleUsingAI in ResourceModuleController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }

    public function cloneResourceModule($slug)
    {
        try {
            // Checking resource module based on slug exists or not
            $getResourceModule = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if (!$getResourceModule) {
                return $this->sendError(__('responses.selected_resource_module_not_found'), 404);
            }
            // Fetching resource module is belongs to current users or not
            if ($getResourceModule->user_id == auth()->user()->id) {
                return $this->sendError(__('responses.selected_resource_module_already_exists'), 403);
            }
            // Fetching Resource module Based on title and resource current users
            $getResourceModuleBasedOnTitle = $this->resourceModuleRepository->getResourceModuleBasedOnTitle($getResourceModule->title);
            if ($getResourceModuleBasedOnTitle) {
                return $this->sendError(__('responses.selected_resource_group_already_exists'));
            }
            // Cloning resource module based on title and resource group id
            $cloneResourceModule = $this->resourceModuleRepository->cloneResourceModule($getResourceModule->id);
            if ($cloneResourceModule) {
                return $this->sendResponse(ResourceModuleResource::make($cloneResourceModule), __('responses.clone_resource_module_successfully'));
            }

            return $this->sendError(__('responses.clone_resource_module_responses_failed'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
