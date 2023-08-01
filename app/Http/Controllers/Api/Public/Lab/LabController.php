<?php

namespace App\Http\Controllers\Api\Public\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Organization\OrganizationResource as PublicOrganizationResource;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\Public\Lab\LabResource;
use App\Repositories\Api\Public\Lab\LabRepository;
class LabController extends AppBaseController
{
    private $labRepository;

    public function __construct(LabRepository $labRepository)
    {
        $this->labRepository = $labRepository;
    }

    public function index(Request $request){
        try {
            $lab = $this->labRepository->getLabList($request);
            if($lab){
                return $this->sendResponse($lab, '');
            }
            return $this->sendError(__('responses.labs_fetched_successfully'), 404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public  function show(Request $request,$slug){
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab){
                return $this->sendResponse(LabResource::make($lab), __('responses.found_organization_list'));
            }
            return $this->sendError(__('responses.organization_not_exists'),404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public  function join(Request $request,$slug){
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot){
                $checkLabActivity=$this->labRepository->checkLabActivity("join",$checkLabExistsOrNot->id);
                if($checkLabActivity){
                    return $this->sendError(__('responses.lab_already_joined'),400);
                }
                $lab = $this->labRepository->joinLab($checkLabExistsOrNot->id);
                if ($lab){
                    return $this->sendResponse([], __('responses.join_lab_successfully'));
                }
            return $this->sendError(__('responses.join_lab_failed'),400);
            }
            return $this->sendError(__('responses.lab_slug_not_found'),404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public  function unJoin(Request $request,$slug){
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot){
                $checkLabActivity=$this->labRepository->checkLabActivity("unjoin",$checkLabExistsOrNot->id);
                if($checkLabActivity){
                    return $this->sendError(__('responses.lab_already_unjoin'),400);
                }
                $lab = $this->labRepository->unJoinLab($checkLabExistsOrNot->id);
                if ($lab){
                    return $this->sendResponse([], __('responses.unjoin_lab_successfully'));
                }
                return $this->sendError(__('responses.unjoin_lab_failed'),400);
            }
            return $this->sendError(__('responses.lab_slug_not_found'),404);
            }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public  function follow(Request $request,$slug){
        try {
        $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
        if ($checkLabExistsOrNot){
            $checkLabActivity=$this->labRepository->checkLabActivity("follow",$checkLabExistsOrNot->id);
            if($checkLabActivity){
                return $this->sendError(__('responses.lab_already_follow'),400);
            }
            $lab = $this->labRepository->followLab($checkLabExistsOrNot->id);
            if ($lab){
                return $this->sendResponse([], __('responses.follow_lab_successfully'));
            }
            return $this->sendError(__('responses.follow_lab_failed'),400);
        }
        return $this->sendError(__('responses.lab_slug_not_found'),404);
        }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'));
        }
    }

    public function unfollow($slug){
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot){
                $checkLabActivity=$this->labRepository->checkLabActivity("unfollow",$checkLabExistsOrNot->id);
                if($checkLabActivity){
                    return $this->sendError(__('responses.lab_already_unfollow'),400);
                }
                $lab = $this->labRepository->unfollowLab($checkLabExistsOrNot->id);
                if ($lab){
                    return $this->sendResponse([], __('responses.unfollow_lab_successfully'));
                }
                return $this->sendError(__('responses.unfollow_lab_failed'),400);
            }
            return $this->sendError(__('responses.lab_slug_not_found'),404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function share($slug){
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLabActivity=$this->labRepository->checkLabActivity("share",$checkLabExistsOrNot->id);
                if($checkLabActivity){
                    return $this->sendError(__('responses.lab_already_share'),400);
                }
                $lab = $this->labRepository->share($checkLabExistsOrNot->id);
                if ($lab){
                    return $this->sendResponse([], __('responses.shared_lab_successfully'));
                }
                return $this->sendError(__('responses.share_lab_failed'),400);
            }
            }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
