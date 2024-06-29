<?php

namespace App\Repositories\Api\Public\Lab;

use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\User;
use App\Services\Manage\MemberManagementService;
use App\Services\Public\LabService;
use App\Services\Public\LabSocialActivitiesService;

class LabRepository implements LabInterface
{
    private $labService;
    private $labSocialActivitiesService;
    private $memberManagementService;

    public function __construct(LabService $labService, LabSocialActivitiesService $labSocialActivitiesService, MemberManagementService $memberManagementService)
    {
        $this->labService = $labService;
        $this->labSocialActivitiesService = $labSocialActivitiesService;
        $this->memberManagementService = $memberManagementService;
    }

    public function getList($request)
    {
        try {
            return $this->labService->getList($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return $this->labService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->labSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkSocialActivity($lab_id, $column, $action)
    {
        try {
            return $this->labSocialActivitiesService->checkSocialActivity($lab_id, $column, $action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function captureSocialActivity($lab_id, $column, $value)
    {
        try {
            return $this->labSocialActivitiesService->captureSocialActivity($lab_id, $column, $value);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function joinLab($lab, $component, $request, $memberList)
    {
        try {
            return $this->memberManagementService->addMembers($lab, $component, $request, $memberList);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function unJoinLab($lab, $component, $request)
    {
        try {
            return $this->memberManagementService->deleteMembers($lab, $component, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkJoinedOrNot($lab, $component)
    {
        try {
            return $this->memberManagementService->checkJoinedOrNot($lab, $component);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getRecordsFromJoinRequest()
    {
        try {
            return $this->memberManagementService->getRecordsFromJoinRequest();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function setJoinRequestParameters($language)
    {
        try {
            return $this->memberManagementService->setJoinRequestParameters($language);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getProjectLabs($request, $challengeId)
    {
        try {
            return $this->labService->getProjectLabs($request, $challengeId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function canJoinLiveEvent(Lab $lab, User $user): bool
    {
        try {
            return $this->labService->canJoinLiveEvent($lab, $user);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function sendLiveEventInvitationLinkToMembers(Lab $lab)
    {
        try {
            return $this->labService->sendLiveEventInvitationLinkToMembers($lab);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function liveEventDetails(Lab $lab)
    {
        try {
            return $this->labService->liveEventDetails($lab);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
