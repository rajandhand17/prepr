<?php

namespace App\Http\Controllers\Api\Manage\Challenge;

use App\Helpers\ChargebeeHelper;
use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Challenge\CreateChallengeAnnouncementRequest;
use App\Http\Requests\Manage\Challenge\CreateChallengeFromResourceUsingAIPreviewRequest;
use App\Http\Requests\Manage\Challenge\CreateChallengeRequest;
use App\Http\Requests\Manage\Challenge\CreateChallengeUsingAIPreviewRequest;
use App\Http\Requests\Manage\Challenge\CreateChallengeUsingAIRequest;
use App\Http\Requests\Manage\Challenge\SelectChallengeWinnerRequest;
use App\Http\Requests\Manage\Challenge\UpdateChallengeAssessmentRequest;
use App\Http\Requests\Manage\Challenge\UpdateChallengeRequest;
use App\Http\Resources\Manage\Challenge\ChallengeAnnouncementResource;
use App\Http\Resources\Manage\Challenge\ChallengeAssessmentResource;
use App\Http\Resources\Manage\Challenge\ChallengeListNameResource;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateChallengeRequest $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            // checks creation limits of the Challenge
            $checkChallengeLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'challenge');
            if ($checkChallengeLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkChallengeCount = $this->challengeRepository->getChallengeCountBasedOnOrganization($checkChallengeLimit['organizationId']);
                if ($checkChallengeLimit['fetchOrganizationPlanDetails'] <= $checkChallengeCount) {
                    return $this->sendError(__('responses.reached_challenge_limit'), 400);
                }
            }

            $uploaded_challenge_cover = config('site-settings.default_challenge_cover_image');
            if ($request->has('cover_banner_type')) {
                switch ($request->cover_banner_type) {
                    case 'image':
                        $uploaded_challenge_cover = $this->challengeRepository->uploadChallengeCoverImage($request->cover_image);
                        if (!$uploaded_challenge_cover) {
                            return $this->sendError(__('responses.image_upload_failed'), 400);
                        }
                        break;
                    case 'embedded':
                        $uploaded_challenge_cover = $request->input('cover_embedded');
                        break;
                }
            }

            $uploaded_achievement_image = config('site-settings.default_challenge_achievement_image');
            if ($request->hasFile('achievement_image') && $request->file('achievement_image')->isValid()) {
                $upload_achievement_image = $this->challengeRepository->uploadChallengeParticipationAchievementImage($request->achievement_image);
                if (!$upload_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $uploaded_achievement_image = $upload_achievement_image;
            }

            $uploaded_assessment_attachment = null;
            if ($request->hasFile('attachments') && $request->file('attachments')->isValid()) {
                $upload_assessment_attachment = $this->challengeRepository->uploadChallengeAssessment($request->attachments);
                if (!$upload_assessment_attachment) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $uploaded_assessment_attachment = $upload_assessment_attachment;
            }

            $createChallenge = $this->challengeRepository->createChallenge($request, $uploaded_challenge_cover, $uploaded_achievement_image, $uploaded_assessment_attachment, $organization);
            if ($createChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($createChallenge), __('responses.challenge_stored_success'), 200);
            }

            return $this->sendError(__('responses.challenge_stored_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if ($challenge) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                if (!$organization) {
                    return $this->sendError(__('responses.selected_organization_not_found'), 404);
                }
                if ($challenge->organization_id != $organization->id) {
                    return $this->sendError(__('responses.challenge_switcher_error'), 403);
                }
                if ($challenge->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_not_accessible'), 403);
                }
                $userId = $userData->id;
                TrackUserProgressHelper::trackChallengeUserProgress($challenge, $userId);

                return $this->sendResponse(ChallengeResource::make($challenge), __('responses.found_challenge_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_challenge_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            $uploaded_challenge_cover = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->media);
            if ($request->has('cover_banner_type')) {
                switch ($request->cover_banner_type) {
                    case 'image':
                        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
                            $uploaded_challenge_cover = $this->challengeRepository->uploadChallengeCoverImage($request->cover_image);
                            if (!$uploaded_challenge_cover) {
                                return $this->sendError(__('responses.image_upload_failed'), 400);
                            }
                        }
                        break;
                    case 'embedded':
                        $uploaded_challenge_cover = $request->input('cover_embedded');
                        break;
                }
            }

            $uploaded_achievement_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->participation_achievement->achievement_image);
            if ($request->hasFile('achievement_image') && $request->file('achievement_image')->isValid()) {
                $upload_achievement_image = $this->challengeRepository->uploadChallengeParticipationAchievementImage($request->achievement_image);
                if (!$upload_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $uploaded_achievement_image = $upload_achievement_image;
            }

            $uploaded_assessment_attachment = ($checkComponentBasedOnSlug->challenge_assessment !== null && is_array($checkComponentBasedOnSlug->challenge_assessment) && count($checkComponentBasedOnSlug->challenge_assessment) > 0) ? str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->challenge_assessment[0]->attachments) : null;
            if ($request->hasFile('attachments') && $request->file('attachments')->isValid()) {
                $upload_assessment_attachment = $this->challengeRepository->uploadChallengeAssessment($request->attachments);
                if (!$upload_assessment_attachment) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $uploaded_assessment_attachment = $upload_assessment_attachment;
            }

            $updateChallenge = $this->challengeRepository->updateChallenge($slug, $request, $uploaded_challenge_cover, $uploaded_achievement_image, $uploaded_assessment_attachment, $organization);
            if ($updateChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($updateChallenge), __('responses.challenge_update_successfully'), 200);
            }

            return $this->sendError(__('responses.challenge_not_update'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }
            $challenge = $this->challengeRepository->deleteChallenge($checkComponentBasedOnSlug->id, $request);
            if ($challenge) {
                return $this->sendResponse(null, __('responses.challenge_delete'));
            }

            return $this->sendError(__('responses.challenge_not_delete'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fetchAssessment($slug, $type = null)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }
            $getChallengeAssessment = [];
            $challenge_assessment_criteria = [];
            if ($checkComponentBasedOnSlug->challenge_assessment) {
                $getChallengeAssessment = $this->challengeRepository->getChallengeAssessmentData($checkComponentBasedOnSlug->challenge_assessment);
            }

            if ($checkComponentBasedOnSlug->challenge_assessment_criteria->isNotEmpty()) {
                $challenge_assessment_criteria = $checkComponentBasedOnSlug->challenge_assessment_criteria->map(function ($item) {
                    return [
                        'assessment_title'        => $item->title,
                        'assessment_description'  => $item->description,
                        'assessment_score'        => $item->score,
                        'assessment_weight'       => $item->weight,
                    ];
                });
            }
            $message = ($type != null) ? __('responses.update_challenge_assessment_detail') : __('responses.found_challenge_assessment_detail');

            if (!empty($getChallengeAssessment) || !empty($challenge_assessment_criteria)) {
                return $this->sendResponse(ChallengeAssessmentResource::make($checkComponentBasedOnSlug), $message, 200);
            }

            $emptyResponse = new stdClass();

            return $this->sendResponse($emptyResponse, __('responses.found_not_challenge_assessment_detail'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateAssessment($slug, UpdateChallengeAssessmentRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }
            $update_assessment_attachment = null;
            if ($checkComponentBasedOnSlug->challenge_assessment) {
                $update_assessment_attachment = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->challenge_assessment->attachments);
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
                return self::fetchAssessment($slug, 'update');
            }

            return $this->sendError(__('responses.challenge_assessment_not_update'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function cloneChallenge($slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }
            // checks creation limits of the Challenge
            $checkChallengeLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($request->organization_id, 'challenge');
            if ($checkChallengeLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkChallengeCount = $this->challengeRepository->getChallengeCountBasedOnOrganization($checkChallengeLimit['organizationId']);
                if ($checkChallengeLimit['fetchOrganizationPlanDetails'] <= $checkChallengeCount) {
                    return $this->sendError(__('responses.reached_challenge_limit'), 400);
                }
            }
            $cloneChallenge = $this->challengeRepository->cloneChallenge($checkComponentBasedOnSlug->id, $organization);

            if ($cloneChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($cloneChallenge), __('responses.challenge_clone_success'), 200);
            }

            return $this->sendError(__('responses.challenge_clone_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            $challenge_timelines = [];
            if ($checkComponentBasedOnSlug->challenge_timelines) {
                if ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '0') {
                    $challenge_timelines = [
                        'timeline_type'                 => 'flexible',
                        'flexible_date_number'          => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_number,
                        'flexible_date_duration'        => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_duration,
                        'automatic_alert'               => $checkComponentBasedOnSlug->challenge_timelines->automatic_alert == '0' ? 'day' : 'week',
                        'flexible_expire_deadline'      => $checkComponentBasedOnSlug->challenge_timelines->flexible_expire_deadline,
                    ];
                } elseif ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '1') {
                    $challenge_timelines = [
                        'timeline_type'                          => 'restricted',
                        'start_date'                             => $checkComponentBasedOnSlug->challenge_timelines->start_date,
                        'start_date_description'                 => $checkComponentBasedOnSlug->challenge_timelines->start_date_description,
                        'registration_deadline_date'             => $checkComponentBasedOnSlug->challenge_timelines->registration_deadline_date,
                        'registration_deadline_date_description' => $checkComponentBasedOnSlug->challenge_timelines->registration_deadline_date_description,
                        'submission_deadline_date'               => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date,
                        'submission_deadline_date_description'   => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date_description,
                        'challenge_duration'                     => $checkComponentBasedOnSlug->challenge_timelines->challenge_duration,
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
            UtilityHelper::logError($e);

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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            $challenge_timelines = [];
            if ($checkComponentBasedOnSlug->challenge_timelines) {
                if ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '0') {
                    $challenge_timelines = [
                        'timeline_type'                 => 'flexible',
                        'flexible_date_number'          => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_number,
                        'flexible_date_duration'        => $checkComponentBasedOnSlug->challenge_timelines->flexible_date_duration,
                        'automatic_alert'               => $checkComponentBasedOnSlug->challenge_timelines->automatic_alert == '0' ? 'day' : 'week',
                        'flexible_expire_deadline'      => $checkComponentBasedOnSlug->challenge_timelines->flexible_expire_deadline,
                    ];
                } elseif ($checkComponentBasedOnSlug->challenge_timelines->timeline_type == '1') {
                    $challenge_timelines = [
                        'timeline_type'                          => 'restricted',
                        'start_date'                             => $checkComponentBasedOnSlug->challenge_timelines->start_date,
                        'start_date_description'                 => $checkComponentBasedOnSlug->challenge_timelines->start_date_description,
                        'registration_deadline_date'             => $checkComponentBasedOnSlug->challenge_timelines->registration_deadline_date,
                        'registration_deadline_date_description' => $checkComponentBasedOnSlug->challenge_timelines->registration_deadline_date_description,
                        'submission_deadline_date'               => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date,
                        'submission_deadline_date_description'   => $checkComponentBasedOnSlug->challenge_timelines->submission_deadline_date_description,
                        'challenge_duration'                     => $checkComponentBasedOnSlug->challenge_timelines->challenge_duration,
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
            UtilityHelper::logError($e);

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
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }
            $challengeAnnouncement = $this->challengeRepository->deleteChallengeAnnouncement($request->announcement_id);
            if ($challengeAnnouncement) {
                return $this->sendResponse(null, __('responses.challenge_announcement_delete'));
            }

            return $this->sendError(__('responses.challenge_announcement_not_delete'), 400);
        } catch (Exception $e) {
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
            $getChallengeListName = $this->challengeRepository->getChallengeListName($request, $organization);
            if ($getChallengeListName) {
                return $this->sendResponse(ChallengeListNameResource::collection($getChallengeListName), __('responses.found_challenges_list'));
            }

            return $this->sendError(__('responses.not_found_challenges_list'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function createChallengeUsingAIPreview(CreateChallengeUsingAIPreviewRequest $request)
    {
        try {
            // checks creation limits of the Challenge
            $checkChallengeLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($request->organization_id, 'challenge');
            if ($checkChallengeLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkChallengeCount = $this->challengeRepository->getChallengeCountBasedOnOrganization($checkChallengeLimit['organizationId']);
                if ($checkChallengeLimit['fetchOrganizationPlanDetails'] <= $checkChallengeCount) {
                    return $this->sendError(__('responses.reached_challenge_limit'), 400);
                }
            }
            $createChallengeUsingAIPreview = $this->challengeRepository->createChallengeUsingAIPreview($request);

            if ($createChallengeUsingAIPreview) {
                return $this->sendResponse($createChallengeUsingAIPreview, __('responses.challenges_previews_created_successfully'), 200);
            } else {
                throw new Exception('createChallengeUsingAIPreview has no value!');
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeUsingAIPreview in ChallengeController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }

    public function createChallengeFromResourceUsingAIPreview(CreateChallengeFromResourceUsingAIPreviewRequest $request)
    {
        try {
            // checks creation limits of the Challenge
            $checkChallengeLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($request->organization_id, 'challenge');
            if ($checkChallengeLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkChallengeCount = $this->challengeRepository->getChallengeCountBasedOnOrganization($checkChallengeLimit['organizationId']);
                if ($checkChallengeLimit['fetchOrganizationPlanDetails'] <= $checkChallengeCount) {
                    return $this->sendError(__('responses.reached_challenge_limit'), 400);
                }
            }
            $createChallengeFromResourceUsingAIPreview = $this->challengeRepository->createChallengeFromResourceUsingAIPreview($request);
            if ($createChallengeFromResourceUsingAIPreview) {
                return $this->sendResponse($createChallengeFromResourceUsingAIPreview, __('responses.challenges_previews_created_successfully'), 200);
            } else {
                throw new Exception('createChallengeFromResourceUsingAIPreview has no value!');
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeFromResourceUsingAIPreview in ChallengeController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }

    public function createChallengeUsingAI(CreateChallengeUsingAIRequest $request)
    {
        try {
            // checks creation limits of the Challenge
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $checkChallengeLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'challenge');
            if ($checkChallengeLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkChallengeCount = $this->challengeRepository->getChallengeCountBasedOnOrganization($checkChallengeLimit['organizationId']);
                if ($checkChallengeLimit['fetchOrganizationPlanDetails'] <= $checkChallengeCount) {
                    return $this->sendError(__('responses.reached_challenge_limit'), 400);
                }
            }

            $upload_cover_image = config('site-settings.default_challenge_cover_image');
            $upload_achievement_image = config('site-settings.default_challenge_achievement_image');
            $upload_assessment_attachment = config('site-settings.default_challenge_cover_image');

            $createChallengeUsingAI = $this->challengeRepository->createChallengeUsingAI($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment);

            if ($createChallengeUsingAI) {
                return $this->sendResponse(ChallengeResource::make($createChallengeUsingAI), __('responses.challenge_created_successfully'), 200);
            } else {
                throw new Exception('createChallengeUsingAI has no value!');
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeUsingAI in ChallengeController.php: '.$e->getMessage());

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }

    public function selectWinner($slug, SelectChallengeWinnerRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }

            if ($checkComponentBasedOnSlug->is_accessible === '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            if ($checkComponentBasedOnSlug->allow_winner_change === '1') {
                return $this->sendError(__('responses.winner_changing_not_allowed'), 403);
            }

            if ($checkComponentBasedOnSlug->winner_select_date != null && $checkComponentBasedOnSlug->winner_select_date > Carbon::now()->toDateTimeString()) {
                return $this->sendError(__('responses.challenge_winner_timeline_fail'), 403);
            }

            if (empty($checkComponentBasedOnSlug->incentive_achievement)) {
                return $this->sendError(__('responses.challenge_incentive_not_found'), 403);
            }

            $selectChallengeWinner = $this->challengeRepository->selectChallengeWinner($checkComponentBasedOnSlug, $request);
            if ($selectChallengeWinner == true) {
                return $this->sendResponse([], __('responses.challenge_winner_selected_successfully'), 200);
            }

            return $this->sendError(__('responses.challenge_winner_selected_not_successfully'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.server_failed'), 500);
        }
    }
}
