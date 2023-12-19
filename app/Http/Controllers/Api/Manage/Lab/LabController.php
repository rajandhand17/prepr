<?php

namespace App\Http\Controllers\Api\Manage\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Lab\CreateLabRequest;
use App\Http\Requests\Manage\Lab\UpdateLabRequest;
use App\Http\Resources\Manage\Lab\LabListNameResource;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Http\Resources\Manage\Lab\LabTemplateResource;
use App\Repositories\Api\Manage\Lab\LabRepository;
use App\Repositories\Api\Manage\LabAchievement\LabAchievementRepository;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Request;

class LabController extends AppBaseController
{
    private LabRepository $labRepository;
    private LabAchievementRepository $labAcheivementRepository;

    public function __construct(LabRepository $labRepository, LabAchievementRepository $labAchievementRepository)
    {
        $this->labRepository = $labRepository;
        $this->labAcheivementRepository = $labAchievementRepository;
    }

    public function index(Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            $lab = $this->labRepository->getLabList($request, $organization);
            if ($lab) {
                $response = [
                    'total_count'  => $lab->total(),
                    'per_page'     => $lab->perPage(),
                    'count'        => $lab->count(),
                    'current_page' => $lab->currentPage(),
                    'total_pages'  => $lab->lastPage(),
                    'list'         => LabResource::collection($lab),
                ];

                return $this->sendResponse($response, __('responses.found_labs_list'));
            }

            return $this->sendError(__('responses.not_found_labs_list'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                return $this->sendResponse(LabResource::make($lab), __('responses.found_labs_list'), 200);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateLabRequest $request)
    {
        try {
            $upload_cover_image = config('site-settings.default_lab_cover_image');
            $upload_achievement_image = null;
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->labRepository->uploadLabCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }

            if ($request->is_achievement_enabled == 'yes') {
                $uploaded_achievement_image = $this->labAcheivementRepository->uploadAcheivementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }

            $createdLab = $this->labRepository->createLab($request, $upload_cover_image, $upload_achievement_image);

            if ($createdLab != false) {
                return $this->sendResponse(LabResource::make($createdLab), __('responses.lab_stored_success'), 200);
            }

            return $this->sendError(__('responses.lab_stored_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function update($slug, UpdateLabRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->labRepository->getLabBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.slug_not_exists'), 403);
            }
            $upload_cover_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->media);
            $upload_achievement_image = null;

            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->labRepository->uploadLabCoverImage($request->cover_image);
                if ($uploaded_cover_image == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            if ($request->is_achievement_enabled == 'yes') {
                $uploaded_achievement_image = $this->labAcheivementRepository->uploadAcheivementImage($request->achievement_image);
                if ($uploaded_achievement_image == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $updateLab = $this->labRepository->updateLab($slug, $request, $upload_cover_image, $upload_achievement_image);
            if ($updateLab != false) {
                return $this->sendResponse(LabResource::make($updateLab), __('responses.lab_update_successfully'), 200);
            }

            return $this->sendError(__('responses.lab_not_update'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->labRepository->checkSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.lab_not_found'), 403);
            }
            $lab = $this->labRepository->deleteLab($checkComponentBasedOnSlug->id, $request);
            if ($lab) {
                return $this->sendResponse(null, __('responses.lab_delete'));
            }

            return $this->sendError(__('responses.lab_not_delete'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkLabSlugExistsOrNot = $this->labRepository->checkSlug($slug);
            if ($checkLabSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.lab_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkLabNameExistsOrNot = $this->labRepository->checkNameExistsOrNot($title);
            if ($checkLabNameExistsOrNot) {
                return $this->sendError(__('responses.lab_name_not_available'));
            }

            return $this->sendResponse([], __('responses.lab_name_available'), 400);
        } catch (\Exception $e) {
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
            $getLabListName = $this->labRepository->getLabListName($request, $organization);
            if ($getLabListName) {
                $response = LabListNameResource::collection($getLabListName);
            }

            return $this->sendResponse($getLabListName, __('responses.found_labs_list'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
