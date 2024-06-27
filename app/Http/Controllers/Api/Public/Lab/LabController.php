<?php

namespace App\Http\Controllers\Api\Public\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Lab\LabListResource;
use App\Http\Resources\Public\Lab\LabNameListResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Models\AirmeetEvent;
use App\Models\User;
use App\Repositories\Api\Public\AirmeetEvent\AirmeetEventRepository;
use App\Repositories\Api\Public\Lab\LabRepository;
use App\Services\Public\ChallengeService;
use Illuminate\Http\Request;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class LabController extends AppBaseController
{
    private $labRepository;

    private $airmeetRepository;

    public function __construct(LabRepository $labRepository, AirmeetEventRepository $airmeetEventRepository)
    {
        $this->labRepository = $labRepository;
        $this->airmeetRepository = $airmeetEventRepository;
    }

    public function index(Request $request)
    {
        try {
            $lab = $this->labRepository->getList($request);
            if ($lab !== false) {
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

            return $this->sendError(__('responses.not_found_labs_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function startPage()
    {
        try {
            $lab = $this->labRepository->getStartList()->take(6);
            return $this->sendResponse(LabListResource::collection($lab),__('responses.found_labs_list'));
        }catch (\Exception $e) {
            dd($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                if ($lab->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_not_accessible'), 403);
                }

                return $this->sendResponse(LabResource::make($lab), __('responses.found_lab_view'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                if ($lab->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_not_accessible'), 403);
                }
                $getColumnNameValue = $this->labRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->labRepository->checkSocialActivity($lab->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_lab'), 400);
                }
                $lab = $this->labRepository->captureSocialActivity($lab->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($lab) {
                    return $this->sendResponse([], __('responses.'.$action.'_lab_successfully'));
                }
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function labList(Request $request)
    {
        try {
            $checkChallenge = ChallengeService::getChallengeBasedOnUUID($request->challenge_id);
            if ($checkChallenge) {
                if ($checkChallenge->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_not_accessible'), 403);
                }
                $getProjectLabList = $this->labRepository->getProjectLabs($request, $checkChallenge->id);
                if ($getProjectLabList) {
                    return $this->sendResponse(LabNameListResource::collection($getProjectLabList), __('responses.found_labs_list'));
                }
            }

            return $this->sendError(__('responses.not_found_labs_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function joinLab($slug, Request $request)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                if ($lab->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_not_accessible'), 403);
                }
                $component = config('constants.lab_component.lab');
                $checkActivity = $this->labRepository->checkJoinedOrNot($lab, $component);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_join_lab'), 400);
                }
                $memberList = $this->labRepository->getRecordsFromJoinRequest();
                if (!$memberList && !count($memberList) > 0) {
                    return $this->sendError(__('responses.send_error'), 404);
                }
                $requestedData = $this->labRepository->setJoinRequestParameters($request->language);
                if (!$requestedData) {
                    return $this->sendError(__('responses.send_error'), 403);
                }
                $joinLab = $this->labRepository->joinLab($lab, $component, $requestedData, $memberList);
                if ($joinLab) {
                    return $this->sendResponse([], __('responses.join_lab_successfully'));
                }

                return $this->sendError(__('responses.join_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function unJoinLab($slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                if ($lab->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_not_accessible'), 403);
                }
                $component = config('constants.lab_component.lab');
                $checkActivity = $this->labRepository->checkJoinedOrNot($lab, $component);
                if ($checkActivity === false) {
                    return $this->sendError(__('responses.already_un_join_lab'), 400);
                }
                $data = new stdClass();
                $data->email = [auth()->user()->email];
                $joinLab = $this->labRepository->unJoinLab($lab, $component, $data);
                if ($joinLab) {
                    return $this->sendResponse([], __('responses.un_join_lab_successfully'));
                }

                return $this->sendError(__('responses.join_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getLiveEventUrl(string $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                /** @var User $authUser */
                $authUser = auth()->user();

                // CHECK PERMISSION
                if ($this->labRepository->canJoinLiveEvent($lab, $authUser)) {
                    /** @var AirmeetEvent|null $airmeet */
                    $airmeet = $lab->airmeet;
                    if (!$airmeet || !$lab->is_live_event_enabled) {
                        return $this->sendError(__('responses.lab_dont_have_live_event_enabled'), Response::HTTP_NOT_FOUND);
                    }

                    /**
                     * LIVE EVENT URL.
                     */
                    $eventUrl = $this->airmeetRepository->getMeetUrl($airmeet, [
                        'user_id'    => $authUser->id,
                        'email'      => data_get($authUser, 'email'),
                        'first_name' => data_get($authUser, 'first_name', data_get($authUser, 'full_name')),
                        'last_name'  => data_get($authUser, 'last_name'),
                    ]);

                    if ($eventUrl !== false) {
                        return $this->sendResponse([
                            'event_url' => $eventUrl,
                        ], __('responses.live_event_url'));
                    }

                    return $this->sendError(__('responses.failed_to_get_live_event_url'), Response::HTTP_BAD_REQUEST);
                }

                return $this->sendError(__('responses.not_allowed_to_join_the_live_event'), Response::HTTP_FORBIDDEN);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.failed_to_get_live_event_url'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function sendLiveEventInvitationLinkToMembers(string $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                $airmeet = $lab->airmeet;
                if (!$airmeet || !$lab->is_live_event_enabled) {
                    return $this->sendError(__('responses.lab_dont_have_live_event_enabled'), Response::HTTP_NOT_FOUND);
                }
                $invitationStatus = $this->labRepository->sendLiveEventInvitationLinkToMembers($lab);
                if ($invitationStatus !== false) {
                    return $this->sendResponse(null, __('responses.send_live_event_invitation_link_to_members'));
                }

                return $this->sendError(__('responses.failed_to_send_live_event_invitation_link_to_members'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.failed_to_send_live_event_invitation_link_to_members'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getLiveEventDetails(string $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                $airmeet = $lab->airmeet;
                if (!$airmeet || !$lab->is_live_event_enabled) {
                    return $this->sendError(__('responses.lab_dont_have_live_event_enabled'), Response::HTTP_NOT_FOUND);
                }
                $eventDetails = $this->labRepository->liveEventDetails($lab);
                if ($eventDetails !== false) {
                    return $this->sendResponse($eventDetails, __('responses.live_event_details'));
                }

                return $this->sendError(__('responses.failed_to_get_live_event_details'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.failed_to_get_live_event_details'), Response::HTTP_BAD_REQUEST);
        }
    }
}
