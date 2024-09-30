<?php

namespace App\Http\Controllers\Api\Public\Achievement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Profile\UserScoreDataResource;
use App\Http\Resources\Public\Achievement\AchievementResource;
use App\Repositories\Api\Public\Achievement\AchievementRepository;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class AchievementController extends AppBaseController
{
    private $achievementRepository;

    public function __construct(AchievementRepository $achievementRepository)
    {
        $this->achievementRepository = $achievementRepository;
    }

    public function index(Request $request)
    {
        try {
            $achievement = $this->achievementRepository->getList($request);
            if ($achievement !== false) {
                $response = [
                    'total_count'   => $achievement->total(),
                    'per_page'      => $achievement->perPage(),
                    'count'         => $achievement->count(),
                    'current_page'  => $achievement->currentPage(),
                    'total_pages'   => $achievement->lastPage(),
                    'user_data'     => UserScoreDataResource::make('static_data'),
                    'list'          => AchievementResource::collection($achievement),
                ];

                return $this->sendResponse($response, __('responses.found_achievement_list'));
            }

            return $this->sendError(__('responses.not_found_achievement_list'), 404);
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getAchievementListBasedOnUsername($username, Request $request)
    {
        try {
            $checkUserExistsOrNot = UserService::getUserByUsername($username);
            if (!$checkUserExistsOrNot) {
                return $this->sendError(__('responses.user_not_found'), 404);
            }
            $userAchievement = $this->achievementRepository->getAchievementList($checkUserExistsOrNot->id, $request);
            if ($userAchievement !== false) {
                $response = [
                    'total_count'  => $userAchievement->total(),
                    'per_page'     => $userAchievement->perPage(),
                    'count'        => $userAchievement->count(),
                    'current_page' => $userAchievement->currentPage(),
                    'total_pages'  => $userAchievement->lastPage(),
                    'list'         => AchievementResource::collection($userAchievement),
                ];

                return $this->sendResponse($response, __('responses.found_achievement_list'));
            }

            return $this->sendError(__('responses.not_found_achievement_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($certificate_id)
    {
        try {
            $achievement = $this->achievementRepository->getAchievementBasedOnCertificateNumber($certificate_id);
            if ($achievement) {
                return $this->sendResponse(AchievementResource::make($achievement), __('responses.found_achievement'));
            }

            return $this->sendError(__('responses.not_found_achievement'), 404);
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function downloadCertificate($certificate_id, Request $request)
    {
        try {
            $downloadFile = $this->achievementRepository->downloadCertificate($certificate_id, $request->format);
            if ($downloadFile) {
                return $this->sendResponse($downloadFile, __('responses.download_successfull'));
            }

            return $this->sendError(__('responses.failed_download_certificate'), 500);
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function achievementActivity($certificate_id, Request $request)
    {
        try {
            $achievement = $this->achievementRepository->getAchievementBasedOnCertificateNumber($certificate_id);
            if ($achievement !== null) {
                $getColumnValue = $this->achievementRepository->getColumnValue($request);
                if (!$getColumnValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }

                $checkActivity = $this->achievementRepository->checkachievementActivity($certificate_id, $getColumnValue['action'], auth()->user()->id);
                switch ($getColumnValue['action']) {
                    case '0':
                        $action = 'unpinned';
                        break;
                    case '1':
                        $action = 'pinned';
                        break;
                    default:
                        $action = null;
                        break;
                }
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_achievement'), 400);
                }

                $achievementAction = $this->achievementRepository->achievementActivity($certificate_id, $getColumnValue['action']);
                if ($achievementAction) {
                    return $this->sendResponse(AchievementResource::make($achievementAction), __('responses.'.$action.'_achievement'));
                }
            }

            return $this->sendError(__('responses.not_found_achievement'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
