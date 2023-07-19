<?php

namespace App\Http\Controllers\Api\Manage\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Lab\CreateLabRequest;
use App\Http\Requests\Manage\Lab\UpdateLabRequest;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Repositories\Api\Manage\Lab\LabRepository;
use App\Repositories\Api\Manage\LabAchievement\LabAchievementRepository;
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
            $lab = $this->labRepository->getLabList($request);
            if ($lab) {
                $response = [
                    'total_count'  => $lab->total(),
                    'per_page'     => $lab->perPage(),
                    'count'        => $lab->count(),
                    'current_page' => $lab->currentPage(),
                    'total_pages'  => $lab->lastPage(),
                    'list'         => LabResource::collection($lab),
                ];

                return $this->sendResponse($response, __('responses.labs_fetched_successfully'));
            }

            return $this->sendError(__('responses.labs_fetched_error'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                return $this->sendResponse(LabResource::make($lab), __('responses.lab_found'), 200);
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
            $upload_cover_image = null;
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
                return $this->sendResponse(LabResource::make($updateLab), __('responses.lab_update_successfull'), 200);
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

            return $this->sendError(__('responses.slug_not_exists'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkLabNameExistsOrNot = $this->labRepository->checkNameExistsOrNot($title);
            if ($checkLabNameExistsOrNot) {
                return $this->sendError(__('responses.lab_name_not_availble'));
            }

            return $this->sendResponse([], __('responses.lab_name_availble'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
