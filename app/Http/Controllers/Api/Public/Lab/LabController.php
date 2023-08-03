<?php

namespace App\Http\Controllers\Api\Public\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Lab\LabResource as  PublicLabResource;
use App\Repositories\Api\Public\Lab\LabRepository;
use Illuminate\Http\Request;

class LabController extends AppBaseController
{
    private $labRepository;

    public function __construct(LabRepository $labRepository)
    {
        $this->labRepository = $labRepository;
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
                    'list'         => PublicLabResource::collection($lab),
                ];

                return $this->sendResponse($response, 'responses.labs_fetched_successfully');
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                return $this->sendResponse(PublicLabResource::make($lab), __('responses.labs_fetched_successfully'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function labSocialActivitiesService($slug, $action)
    {
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLab = $this->labRepository->checkLabActivity($checkLabExistsOrNot->id, $action);
                if ($checkLab !== null && isset($checkLab->id)) {
                    return $this->sendError(__('responses.lab_already_joined'), 400);
                }
                if ($checkLab == false) {
                    dd($action);
                }
                $lab = $this->labRepository->labSocialActivitiesService($checkLabExistsOrNot->id, $checkLab['column'], $checkLab['action']);
                if ($lab) {
                    return $this->sendResponse([], __('responses.follow_organization_successfully'));
                }
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            dd($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function join(Request $request, $slug)
    {
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLabActivity = $this->labRepository->checkLabActivity('join', $checkLabExistsOrNot->id);
                if ($checkLabActivity) {
                    return $this->sendError(__('responses.lab_already_joined'), 400);
                }
                $lab = $this->labRepository->joinLab($checkLabExistsOrNot->id);
                if ($lab) {
                    return $this->sendResponse([], __('responses.join_lab_successfully'));
                }

                return $this->sendError(__('responses.join_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function unJoin(Request $request, $slug)
    {
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLabActivity = $this->labRepository->checkLabActivity('unjoin', $checkLabExistsOrNot->id);
                if ($checkLabActivity) {
                    return $this->sendError(__('responses.lab_already_unjoin'), 400);
                }
                $lab = $this->labRepository->unJoinLab($checkLabExistsOrNot->id);
                if ($lab) {
                    return $this->sendResponse([], __('responses.unjoin_lab_successfully'));
                }

                return $this->sendError(__('responses.unjoin_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function follow(Request $request, $slug)
    {
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLabActivity = $this->labRepository->checkLabActivity('follow', $checkLabExistsOrNot->id);
                if ($checkLabActivity) {
                    return $this->sendError(__('responses.lab_already_follow'), 400);
                }
                $lab = $this->labRepository->followLab($checkLabExistsOrNot->id);
                if ($lab) {
                    return $this->sendResponse([], __('responses.follow_lab_successfully'));
                }

                return $this->sendError(__('responses.follow_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'));
        }
    }

    public function unfollow($slug)
    {
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLabActivity = $this->labRepository->checkLabActivity('unfollow', $checkLabExistsOrNot->id);
                if ($checkLabActivity) {
                    return $this->sendError(__('responses.lab_already_unfollow'), 400);
                }
                $lab = $this->labRepository->unfollowLab($checkLabExistsOrNot->id);
                if ($lab) {
                    return $this->sendResponse([], __('responses.unfollow_lab_successfully'));
                }

                return $this->sendError(__('responses.unfollow_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function share($slug)
    {
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLabActivity = $this->labRepository->checkLabActivity('share', $checkLabExistsOrNot->id);
                if ($checkLabActivity) {
                    return $this->sendError(__('responses.lab_already_share'), 400);
                }
                $lab = $this->labRepository->share($checkLabExistsOrNot->id);
                if ($lab) {
                    return $this->sendResponse([], __('responses.shared_lab_successfully'));
                }

                return $this->sendError(__('responses.share_lab_failed'), 400);
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
