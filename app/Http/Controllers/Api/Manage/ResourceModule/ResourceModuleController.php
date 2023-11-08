<?php

namespace App\Http\Controllers\Api\Manage\ResourceModule;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceModule\AddLinksResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\CreateResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\DeleteMediaResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\FileUploadResourceModuleRequest;
use App\Http\Requests\Manage\ResourceModule\UpdateResourceModuleRequest;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleListNameResource;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Request;

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
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
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
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            if (!auth()->user()->isAbleTo('view_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $responseView = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($responseView) {
                return $this->sendResponse(ResourceModuleResource::make($responseView), __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_view'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateResourceModuleRequest $request)
    {
        try {
            if (!auth()->user()->isAbleTo('create_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $upload_cover_image = config('site-settings.default_resource_module_cover_image');
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->resourceModuleRepository->uploadResourceModuleCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $createResourceModule = $this->resourceModuleRepository->createResourceModule($request, $upload_cover_image);
            if ($createResourceModule) {
                return $this->sendResponse(__('responses.resource_module_stored_success'), 200);
            }

            return $this->sendError(__('responses.resource_module_stored_failed'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update(UpdateResourceModuleRequest $request, $slug)
    {
        try {
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            if (!auth()->user()->isAbleTo('edit_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $upload_cover_image = config('site-settings.default_resource_module_cover_image');
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->resourceModuleRepository->uploadResourceModuleCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $updateResourceModule = $this->resourceModuleRepository->updateResourceModule($slug, $request, $upload_cover_image);
            if ($updateResourceModule) {
                return $this->sendResponse($updateResourceModule, __('responses.resource_module_update_success'), 200);
            }

            return $this->sendError(__('responses.resource_module_stored_failed'), 403);
        } catch(\Exception $e) {
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

            return $this->sendResponse([], __('responses.resource_module_name_available'), 400);
        } catch(\Exception $e) {
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

            return $this->sendResponse([], __('responses.resource_module_slug_available'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addLinksAndEmbedMedia(AddLinksResourceModuleRequest $request, $slug)
    {
        try {
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            if (!auth()->user()->isAbleTo('create_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            if ($request->has('add_links') && !empty($request->add_links)) {
                $addLinks = $this->resourceModuleRepository->addLinks($request, $checkResourceModuleSlugExistsOrNot->id);
                if (!$addLinks) {
                    return $this->sendError(__('responses.add_links_failed'), 403);
                }
            }
            if ($request->has('add_embedded_media') && !empty($request->add_embedded_media)) {
                $addEmbeddedMedia = $this->resourceModuleRepository->addEmbeddedMedia($request, $checkResourceModuleSlugExistsOrNot->id);
                if (!$addEmbeddedMedia) {
                    return $this->sendError(__('responses.add_embedded_media_failed'), 403);
                }
            }

            return $this->sendResponse(__('responses.add_links_success'), 200);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fileUpload(FileUploadResourceModuleRequest $request, $slug)
    {
        try {
            $type = config('constants.resource_module_type.image');
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            if (!auth()->user()->isAbleTo('create_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $insertData = $this->resourceModuleRepository->fileUpload($request, $checkResourceModuleSlugExistsOrNot->id, $type);
            if ($insertData) {
                return $this->sendResponse(__('responses.file_upload_success'), 200);
            }

            return $this->sendError(__('responses.file_upload_failed'), 500);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug)
    {
        try {
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            if (!auth()->user()->isAbleTo('delete_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $deleteResourceModule = $this->resourceModuleRepository->deleteResourceModule($slug, $checkResourceModuleSlugExistsOrNot->id);
            if ($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.resource_module_delete'));
            }

            return $this->sendError(__('responses.resource_module_not_delete'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteMedia(DeleteMediaResourceModuleRequest $request, $slug)
    {
        try {
            $type = config('constants.resource_module_type.image');
            $checkResourceModuleSlugExistsOrNot = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($checkResourceModuleSlugExistsOrNot == false) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }
            if (!auth()->user()->isAbleTo('delete_resource_module')) {
                return $this->sendError(__('responses.permission_forbidden'), 403);
            }
            $deleteResourceModule = $this->resourceModuleRepository->deleteResourceModuleMedia($request, $checkResourceModuleSlugExistsOrNot->id, $type);
            if ($deleteResourceModule) {
                return $this->sendResponse(null, __('responses.resource_module_media_delete'));
            }

            return $this->sendError(__('responses.resource_module_media_not_delete'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getList(Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            $responseModuleList = $this->resourceModuleRepository->getListName($request, $organization);
            if ($responseModuleList) {
                return $this->sendResponse(ResourceModuleListNameResource::collection($responseModuleList), __('responses.found_resource_module_list'));
            }
            return $this->sendError(__('responses.not_found_resource_module_view'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
