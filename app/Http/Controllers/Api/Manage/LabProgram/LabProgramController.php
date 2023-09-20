<?php

namespace App\Http\Controllers\Api\Manage\LabProgram;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\LabProgram\CreateLabProgramRequest;
use App\Http\Requests\Manage\LabProgram\UpdateLabProgramRequest;
use App\Http\Resources\Manage\LabProgram\LabProgramResource;
use App\Repositories\Api\Manage\LabProgram\LabProgramRepository;
use App\Repositories\Api\Manage\LabProgramAchievement\LabProgramAchievementRepository;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Request;

class LabProgramController extends AppBaseController
{
    private $labProgramRepository;

    private $labProgramAchievements;

    public function __construct(LabProgramRepository $labProgramRepository, LabProgramAchievementRepository $labProgramAchievements)
    {
        $this->labProgramRepository = $labProgramRepository;
        $this->labProgramAchievements = $labProgramAchievements;
    }

    public function index(Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            $listLabProgram = $this->labProgramRepository->getLabProgramList($request, $organization);

            if ($listLabProgram) {
                $response = [
                    'total_count'  => $listLabProgram->total(),
                    'per_page'     => $listLabProgram->perPage(),
                    'count'        => $listLabProgram->count(),
                    'current_page' => $listLabProgram->currentPage(),
                    'total_pages'  => $listLabProgram->lastPage(),
                    'list'         => LabProgramResource::collection($listLabProgram),
                ];
                return $this->sendResponse($response, __('responses.found_lab_program_list'));
            }
            return $this->sendError(__('responses.not_found_lab_program_list'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram) {
                return $this->sendResponse(LabProgramResource::make($labProgram), __('responses.found_lab_program_view'));
            }

            return $this->sendError(__('responses.not_found_lab_program_view'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateLabProgramRequest $request)
    {
        try {
            $upload_media = config('site-settings.default_lab_program_profile_image');
            if ($request->media !== null) {
                $uploaded_media = $this->labProgramRepository->uploadLabProgramMedia($request->media);
                if (!$uploaded_media) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_media = $uploaded_media;
            }
            $upload_achievement_image = config('site-settings.default_lab_program_profile_image');
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->labProgramAchievements->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $createLabProgram = $this->labProgramRepository->createLabProgram($request, $upload_media, $upload_achievement_image);
            if ($createLabProgram) {
                return $this->sendResponse($createLabProgram, __('responses.lab_program_stored_success'), 200);
            }

            return $this->sendError(__('responses.lab_program_stored_failed'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update($slug, UpdateLabProgramRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.slug_not_exists'), 403);
            }
            $upload_media = config('site-settings.default_lab_program_profile_image');
            if ($request->media !== null) {
                $uploaded_media = $this->labProgramRepository->uploadLabProgramMedia($request->media);
                if (!$uploaded_media) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_media = $uploaded_media;
            }
            $upload_achievement_image = null;
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->labProgramAchievements->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $updateLabProgram = $this->labProgramRepository->updateLabProgram($slug, $request, $upload_media, $upload_achievement_image);
            if ($updateLabProgram) {
                return $this->sendResponse($updateLabProgram, __('responses.lab_program_update_successfully'), 200);
            }

            return $this->sendError(__('responses.lab_program_not_update'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug, Request $request)
    {
        try {
            $checkLabProgramSlugExistsOrNot = $this->labProgramRepository->checkSlug($slug);
            if ($checkLabProgramSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.lab_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkNameLabProgram = $this->labProgramRepository->checkNameExistsOrNot($title);
            if ($checkNameLabProgram == false) {
                return $this->sendResponse([], __('responses.lab_program_name_available'));
            }

            return $this->sendError(__('responses.lab_program_name_not_available'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug, Request $request)
    {
        try {
            $checkLabProgramSlugExistsOrNot = $this->labProgramRepository->checkSlug($slug);
            if ($checkLabProgramSlugExistsOrNot == false) {
                return $this->sendError(__('responses.lab_program_not_found'), 404);
            }
            $deletLabProgram = $this->labProgramRepository->delete($slug);
            if ($deletLabProgram) {
                return $this->sendResponse(null, __('responses.lab_program_delete'));
            }

            return $this->sendError(__('responses.lab_program_not_delete'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
