<?php

namespace App\Http\Controllers\Api\Manage\Lab;

use App\Helpers\ChargebeeHelper;
use App\Helpers\MixpanelHelper;
use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Lab\CreateLabRequest;
use App\Http\Requests\Manage\Lab\CreateLabUsingAIPreviewRequest;
use App\Http\Requests\Manage\Lab\CreateLabUsingAIRequest;
use App\Http\Requests\Manage\Lab\UpdateLabRequest;
use App\Http\Resources\Manage\Lab\LabListNameResource;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Repositories\Api\Manage\Lab\LabRepository;
use App\Repositories\Api\Manage\LabAchievement\LabAchievementRepository;
use App\Services\LastVisitedActivityModuleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
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
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                if (!$organization) {
                    return $this->sendError(__('responses.selected_organization_not_found'), 404);
                }
                if ($lab->organization_id != $organization->id) {
                    return $this->sendError(__('responses.lab_switcher_error'), 403);
                }
                if ($lab->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_not_accessible'), 403);
                }

                // For user progress tracking
                $userId = $userData->id;
                TrackUserProgressHelper::trackLabUserProgress($lab, $userId);

                MixpanelHelper::mixpanel_tracking(config('mixpanel.view_lab'), $lab, auth()->user(), request()->ip());

                // For last visited activity tracking
                $joined_status = $lab->joined();
                if ($joined_status != 'NA' && $joined_status != null) {
                    if ($joined_status->invite_status == '1') {
                        $moduleType = config('constants.module_type.labs');
                        LastVisitedActivityModuleService::lastVisitedActivityModule($lab->id, $userId, $moduleType);
                    }
                }

                return $this->sendResponse(LabResource::make($lab), __('responses.found_labs_list'), 200);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateLabRequest $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            // checks creation limits of the Lab
            $checkLabLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'lab');
            if ($checkLabLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkLabCount = $this->labRepository->getLabCountBasedOnOrganization($checkLabLimit['organizationId']);
                if ($checkLabLimit['fetchOrganizationPlanDetails'] <= $checkLabCount) {
                    return $this->sendError(__('responses.reached_lab_limit'), 400);
                }
            }
            $upload_cover_image = config('site-settings.default_lab_cover_image');
            if ($request->cover_image !== null) {
                if ($request->media_type == 'image') {
                    if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
                        $uploaded_cover_image = $this->labRepository->uploadLabCoverImage($request->cover_image);
                        if (!$uploaded_cover_image) {
                            return $this->sendError(__('responses.image_upload_failed'), 400);
                        }
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_image = $request->cover_image;
                }
                $upload_cover_image = $uploaded_cover_image;
            }

            $upload_achievement_image = null;
            if ($request->is_achievement_enabled == 'yes') {
                $uploaded_achievement_image = $this->labAcheivementRepository->uploadAcheivementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }

            $createdLab = $this->labRepository->createLab($request, $upload_cover_image, $upload_achievement_image, $organization);

            if ($createdLab != false) {
                return $this->sendResponse(LabResource::make($createdLab), __('responses.lab_stored_success'), 200);
            }

            return $this->sendError(__('responses.lab_stored_failed'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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

            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.lab_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.lab_not_accessible'), 403);
            }
            $upload_cover_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->media);
            if ($request->cover_image !== null) {
                if ($request->media_type == 'image') {
                    if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
                        $uploaded_cover_image = $this->labRepository->uploadLabCoverImage($request->cover_image);
                        if (!$uploaded_cover_image) {
                            return $this->sendError(__('responses.image_upload_failed'), 400);
                        }
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_image = $request->cover_image;
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $upload_achievement_image = null;
            if ($request->is_achievement_enabled == 'yes') {
                if ($request->hasFile('achievement_image') && $request->file('achievement_image')->isValid()) {
                    $uploaded_achievement_image = $this->labAcheivementRepository->uploadAcheivementImage($request->achievement_image);
                    if ($uploaded_achievement_image == false) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                    $upload_achievement_image = $uploaded_achievement_image;
                }
            }
            $updateLab = $this->labRepository->updateLab($slug, $request, $upload_cover_image, $upload_achievement_image, $organization);
            if ($updateLab != false) {
                return $this->sendResponse(LabResource::make($updateLab), __('responses.lab_update_successfully'), 200);
            }

            return $this->sendError(__('responses.lab_not_update'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.lab_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.lab_not_accessible'), 403);
            }
            $lab = $this->labRepository->deleteLab($checkComponentBasedOnSlug->id, $request);
            if ($lab) {
                return $this->sendResponse(null, __('responses.lab_delete'));
            }

            return $this->sendError(__('responses.lab_not_delete'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

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
            $getLabListName = $this->labRepository->getLabListName($request, $organization);
            if ($getLabListName) {
                $response = LabListNameResource::collection($getLabListName);
            }

            return $this->sendResponse($getLabListName, __('responses.found_labs_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function createLabUsingAIPreview(CreateLabUsingAIPreviewRequest $request)
    {
        try {
            // checks creation limits of the Lab
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $checkLabLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'lab');
            if ($checkLabLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkLabCount = $this->labRepository->getLabCountBasedOnOrganization($checkLabLimit['organizationId']);
                if ($checkLabLimit['fetchOrganizationPlanDetails'] <= $checkLabCount) {
                    return $this->sendError(__('responses.reached_lab_limit'), 400);
                }
            }
            $createLabUsingAIPreview = $this->labRepository->createLabUsingAIPreview($request);

            if ($createLabUsingAIPreview) {
                return $this->sendResponse($createLabUsingAIPreview, __('responses.labs_previews_created_successfully'), 200);
            } else {
                throw new Exception('createLabUsingAIPreview has no value!');
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createLabUsingAIPreview in LabController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }

    public function createLabUsingAI(CreateLabUsingAIRequest $request)
    {
        try {
            // checks creation limits of the Lab
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $checkLabLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'lab');
            if ($checkLabLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkLabCount = $this->labRepository->getLabCountBasedOnOrganization($checkLabLimit['organizationId']);
                if ($checkLabLimit['fetchOrganizationPlanDetails'] <= $checkLabCount) {
                    return $this->sendError(__('responses.reached_lab_limit'), 400);
                }
            }
            $upload_cover_image = config('site-settings.default_lab_cover_image');
            $upload_achievement_image = config('site-settings.default_achievement_image');

            $createLabUsingAI = $this->labRepository->createLabUsingAI($request, $upload_cover_image, $upload_achievement_image, $organization);

            if ($createLabUsingAI) {
                return $this->sendResponse(LabResource::make($createLabUsingAI), __('responses.lab_created_successfully'), 200);
            } else {
                throw new Exception('createLabUsingAI has no value!');
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createLabUsingAI in LabController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }
}
