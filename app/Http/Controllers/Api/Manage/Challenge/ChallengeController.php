<?php

namespace App\Http\Controllers\Api\Manage\Challenge;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Challenge\CreateChallengeAnnouncementRequest;
use App\Http\Requests\Manage\Challenge\CreateChallengeRequest;
use App\Http\Requests\Manage\Challenge\UpdateChallengeRequest;
use App\Http\Resources\Manage\Challenge\ChallengeAnnouncementResource;
use App\Http\Resources\Manage\Challenge\ChallengeAssessmentResource;
use App\Http\Resources\Manage\Challenge\ChallengeListNameResource;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use App\Services\Manage\OrganizationService;
use Exception;
use Illuminate\Http\Request;
use stdClass;

class ChallengeController extends AppBaseController
{
    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }

    public function index(Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }

            $challenge = $this->challengeRepository->getChallengeList($request, $organization);
            if ($challenge) {
                $response = [
                    'total_count'  => $challenge->total(),
                    'per_page'     => $challenge->perPage(),
                    'count'        => $challenge->count(),
                    'current_page' => $challenge->currentPage(),
                    'total_pages'  => $challenge->lastPage(),
                    'list'         => ChallengeResource::collection($challenge),
                ];

                return $this->sendResponse($response, __('responses.found_challenges_list'));
            }

            return $this->sendError(__('responses.not_found_challenges_list'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateChallengeRequest $request)
    {
        try {
            // if (!auth()->user()->isAbleTo('create_challenge')) {
            //     return $this->sendError(__('responses.permission_forbidden'), 403);
            // }
            $upload_cover_image = config('site-settings.default_challenge_cover_image');
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->challengeRepository->uploadChallengeCoverImage($request->cover_image);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }

            $upload_achievement_image = config('site-settings.default_challenge_achievement_image');
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->challengeRepository->uploadChallengeParticipationAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }

            $upload_assessment_attachment = config('site-settings.default_challenge_cover_image');
            if ($request->attachments !== null) {
                $uploaded_assessment_attachment = $this->challengeRepository->uploadChallengeAssessment($request->attachments);
                if (!$uploaded_assessment_attachment) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_assessment_attachment = $uploaded_assessment_attachment;
            }

            $createChallenge = $this->challengeRepository->createChallenge($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment);

            if ($createChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($createChallenge), __('responses.challenge_stored_success'), 200);
            }

            return $this->sendError(__('responses.challenge_stored_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            // if (!auth()->user()->isAbleTo('view_challenge', $challenge)) {
            //     return $this->sendError(__('responses.permission_forbidden'), 403);
            // }
            if ($challenge) {
                return $this->sendResponse(ChallengeResource::make($challenge), __('responses.found_challenge_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_challenge_detail'), 404);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update($slug, UpdateChallengeRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $update_cover_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->media);
            $update_participation_achievement_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->participation_achievement->achievement_image);
            $update_assessment_attachment = ($checkComponentBasedOnSlug->challenge_assessment !== null && is_array($checkComponentBasedOnSlug->challenge_assessment) && count($checkComponentBasedOnSlug->challenge_assessment) > 0) ? str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->challenge_assessment[0]->attachments) : null;
            if ($request->cover_image !== null) {
                $uploaded_cover_image = $this->challengeRepository->uploadChallengeCoverImage($request->cover_image);
                if ($uploaded_cover_image == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $update_cover_image = $uploaded_cover_image;
            }

            if ($request->achievement_image !== null) {
                $updated_challenge_achievement_image = $this->challengeRepository->uploadChallengeParticipationAchievementImage($request->achievement_image);
                if ($updated_challenge_achievement_image == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $update_participation_achievement_image = $updated_challenge_achievement_image;
            }

            if ($request->attachments !== null) {
                $updated_assessment_attachment = $this->challengeRepository->uploadChallengeAssessment($request->attachments);
                if ($updated_assessment_attachment == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $update_assessment_attachment = $updated_assessment_attachment;
            }

            $updateChallenge = $this->challengeRepository->updateChallenge($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment);
            if ($updateChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($updateChallenge), __('responses.challenge_update_successfully'), 200);
            }

            return $this->sendError(__('responses.challenge_not_update'));
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            // if (!auth()->user()->isAbleTo('delete_challenge', $checkComponentBasedOnSlug)) {
            //     return $this->sendError(__('responses.challenge_delete_access_denied'), 403);
            // }
            $challenge = $this->challengeRepository->deleteChallenge($checkComponentBasedOnSlug->id, $request);
            if ($challenge) {
                return $this->sendResponse(null, __('responses.challenge_delete'));
            }

            return $this->sendError(__('responses.challenge_not_delete'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkChallengeSlugExistsOrNot = $this->challengeRepository->checkSlug($slug);
            if ($checkChallengeSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.challenge_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkChallengeNameExistsOrNot = $this->challengeRepository->checkNameExistsOrNot($title);
            if ($checkChallengeNameExistsOrNot) {
                return $this->sendError(__('responses.challenge_name_not_available'));
            }

            return $this->sendResponse([], __('responses.challenge_name_available'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchAssessment($slug)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $getChallengeAssessment = [];
            $challenge_assessment_criteria = [];
            if ($checkComponentBasedOnSlug->challenge_assessment->isNotEmpty()) {
                $getChallengeAssessment = $this->challengeRepository->getChallengeAssessmentData($checkComponentBasedOnSlug->challenge_assessment);
            }

            if ($checkComponentBasedOnSlug->challenge_assessment_criteria->isNotEmpty()) {
                $challenge_assessment_criteria = $checkComponentBasedOnSlug->challenge_assessment_criteria->map(function ($item) {
                    return [
                        'assessment_title'   => $item->title,
                        'assessment_score'   => $item->score,
                        'assessment_weight'  => $item->weight,
                    ];
                });
            }

            if (!empty($getChallengeAssessment) || !empty($challenge_assessment_criteria)) {
                return $this->sendResponse(ChallengeAssessmentResource::make($checkComponentBasedOnSlug), __('responses.found_challenge_assessment_detail'), 200);
            }

            $emptyResponse = new stdClass();

            return $this->sendResponse($emptyResponse, __('responses.found_not_challenge_assessment_detail'));
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateAssessment($slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            if ($checkComponentBasedOnSlug->challenge_assessment->isNotEmpty()) {
                $update_assessment_attachment = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->challenge_assessment[0]->attachments);
            }

            if ($request->attachments !== null) {
                $updated_assessment_attachment = $this->challengeRepository->uploadChallengeAssessment($request->attachments);
                if ($updated_assessment_attachment == false) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $update_assessment_attachment = $updated_assessment_attachment;
            }

            $updateChallengeAssessment = $this->challengeRepository->updateChallengeAssessment($checkComponentBasedOnSlug->id, $update_assessment_attachment, $request);
            if ($updateChallengeAssessment['updateChallengeAssessmentCriteria'] && $updateChallengeAssessment['updateChallengeAssessment']) {
                return self::fetchAssessment($slug);
            }

            return $this->sendError(__('responses.challenge_assessment_not_update'));
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function cloneChallenge($slug, Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $cloneChallenge = $this->challengeRepository->cloneChallenge($checkComponentBasedOnSlug->id, $organization);

            if ($cloneChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($cloneChallenge), __('responses.challenge_clone_success'), 200);
            }

            return $this->sendError(__('responses.challenge_clone_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function createAnnouncement($slug, CreateChallengeAnnouncementRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }

            $challenge_timelines = [];
            if ($checkComponentBasedOnSlug->challenge_timelines) {
                if ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '0') {
                    $challenge_timelines = [
                        'timeline_type'                 => 'flexible',
                        'flexible_date_number'          => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_number,
                        'flexible_date_duration'        => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_duration,
                        'automatic_alert'               => $checkComponentBasedOnSlug->challenge_timelines->automatic_alert,
                        'flexible_expire_deadline'      => $checkComponentBasedOnSlug->challenge_timelines->flexible_expire_deadline,
                    ];
                } elseif ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '1') {
                    $challenge_timelines = [
                        'timeline_type'                         => 'restricted',
                        'open_call_date'                        => $checkComponentBasedOnSlug->challenge_timelines->open_call_date,
                        'open_call_date_description'            => $checkComponentBasedOnSlug->challenge_timelines->open_call_date_description,
                        'last_call_date'                        => $checkComponentBasedOnSlug->challenge_timelines->last_call_date,
                        'last_call_date_description'            => $checkComponentBasedOnSlug->challenge_timelines->last_call_date_description,
                        'application_deadline_date'             => $checkComponentBasedOnSlug->challenge_timelines->application_deadline_date,
                        'application_deadline_date_description' => $checkComponentBasedOnSlug->challenge_timelines->application_deadline_date_description,
                        'submission_deadline_date'              => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date,
                        'submission_deadline_date_description'  => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date_description,
                        'challenge_duration'                    => $checkComponentBasedOnSlug->challenge_timelines->challenge_duration,
                    ];
                }
            }

            $createAnnouncement = $this->challengeRepository->createChallengeAnnouncement($checkComponentBasedOnSlug->id, $request);
            if ($createAnnouncement != false) {
                $response = [
                    'slug'                      => $checkComponentBasedOnSlug->slug,
                    'title'                     => $checkComponentBasedOnSlug->title,
                    'challenge_timline'         => $challenge_timelines,
                    'challenge_announcement'    => ChallengeAnnouncementResource::make($createAnnouncement),
                ];

                return $this->sendResponse($response, __('responses.challenge_announcement_created'));
            }

            return $this->sendError(__('responses.challenge_announcement_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function listAnnouncement($slug)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }

            $challenge_timelines = [];
            if ($checkComponentBasedOnSlug->challenge_timelines) {
                if ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '0') {
                    $challenge_timelines = [
                        'timeline_type'                 => 'flexible',
                        'flexible_date_number'          => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_number,
                        'flexible_date_duration'        => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_duration,
                        'automatic_alert'               => $checkComponentBasedOnSlug->challenge_timelines->automatic_alert,
                        'flexible_expire_deadline'      => $checkComponentBasedOnSlug->challenge_timelines->flexible_expire_deadline,
                    ];
                } elseif ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '1') {
                    $challenge_timelines = [
                        'timeline_type'                         => 'restricted',
                        'open_call_date'                        => $checkComponentBasedOnSlug->challenge_timelines->open_call_date,
                        'open_call_date_description'            => $checkComponentBasedOnSlug->challenge_timelines->open_call_date_description,
                        'last_call_date'                        => $checkComponentBasedOnSlug->challenge_timelines->last_call_date,
                        'last_call_date_description'            => $checkComponentBasedOnSlug->challenge_timelines->last_call_date_description,
                        'application_deadline_date'             => $checkComponentBasedOnSlug->challenge_timelines->application_deadline_date,
                        'application_deadline_date_description' => $checkComponentBasedOnSlug->challenge_timelines->application_deadline_date_description,
                        'submission_deadline_date'              => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date,
                        'submission_deadline_date_description'  => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date_description,
                        'challenge_duration'                    => $checkComponentBasedOnSlug->challenge_timelines->challenge_duration,
                    ];
                }
            }

            $response = [
                'slug'                      => $checkComponentBasedOnSlug->slug,
                'title'                     => $checkComponentBasedOnSlug->title,
                'challenge_timline'         => $challenge_timelines,
                'challenge_announcement'    => ChallengeAnnouncementResource::collection($checkComponentBasedOnSlug->challenge_announcement),
            ];

            return $this->sendResponse($response, __('responses.found_challenges_announcement'));
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteAnnouncement($slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $challengeAnnouncement = $this->challengeRepository->deleteChallengeAnnouncement($request->announcement_id);
            if ($challengeAnnouncement) {
                return $this->sendResponse(null, __('responses.challenge_announcement_delete'));
            }

            return $this->sendError(__('responses.challenge_announcement_not_delete'), 400);
        } catch (Exception $e) {
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
            $getChallengeListName = $this->challengeRepository->getChallengeListName($request, $organization);
            if ($getChallengeListName) {
                return $this->sendResponse(ChallengeListNameResource::collection($getChallengeListName), __('responses.found_challenges_list'));
            }

            return $this->sendError(__('responses.not_found_challenges_list'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
