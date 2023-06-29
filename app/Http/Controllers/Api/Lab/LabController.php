<?php

namespace App\Http\Controllers\Api\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Lab\CheckLabNameRequest;
use App\Http\Requests\Lab\CheckLabSlugRequest;
use App\Http\Requests\Lab\LabJoinRequest;
use App\Http\Requests\Lab\LabStoreRequest;
use App\Http\Requests\LabfollowRequest;
use App\Http\Resources\Lab\LabResource;
use App\Repositories\Api\Lab\LabRepository;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;

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
            $list = $this->labRepository->list($request);
            if ($list) {
                return $this->sendResponse(LabResource::collection($list), __('responses.lab'));
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function view(Request $request)
    {
        try {
            $view = $this->labRepository->view($request);
            if ($view) {
                return $this->sendResponse($view, __('responses.lab'));
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function create(Request $request)
    {
        try {
            $create = $this->labRepository->create($request);
            if ($create) {
                return $create;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function store(LabStoreRequest $request)
    {
    try {
        if ($request->cover_image !== null) {
            $upload_cover_image = $this->labRepository->uploadImage($request->cover_image,"lab");
            if ($upload_cover_image == false) {
                return $this->sendError(__('responses.fail_organization_image_upload'), 400);
            }
            $upload_cover_image = $upload_cover_image;
        }
        if($request->achievement_en_switch=="yes"){
            $upload_acheivements_image=$this->labRepository->uploadImage($request->achievement_image,"achievement");
            if ($upload_acheivements_image == false) {
                return $this->sendError(__('responses.fail_organization_image_upload'), 400);
            }
            $upload_acheivements_image = $upload_acheivements_image;
        }else{
            $upload_acheivements_image=null;
        }
        $component="lab";
        $store = $this->labRepository->store($component,$request,$upload_cover_image,$upload_acheivements_image);
        if ($store!=false) {
            return $this->sendResponse([],__('responses.lab_stored_success'),200);
        }
        return $this->sendError(__('responses.lab_stored_failed'), 400);
    } catch (\Exception $e) {
        return $this->sendError(__('responses.send_error'), 500);
    }
    }

    public function delete(Request $request)
    {
        try {
            $delete = $this->labRepository->delete($request);
            if ($delete) {
                return $delete;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function labDetail(Request $request)
    {
        try {
            $lab_detail = $this->labRepository->labDetail($request->id);
            if ($lab_detail) {
                return $lab_detail;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkLabSlug(CheckLabSlugRequest $request)
    {
        try {
            $check_lab_slug = $this->labRepository->checkLabSlug($request);
            if ($check_lab_slug) {
                return $check_lab_slug;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkLabName(CheckLabNameRequest $request)
    {
        try {
            $check_lab_name = $this->labRepository->checkLabName($request);
            if ($check_lab_name) {
                return $check_lab_name;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getSkills($request)
    {
        try {
            $skills = $this->labRepository->getSkills($request);
            if ($skills) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTags(Request $request)
    {
        try {
            $tags = $this->labRepository->getTags($request->lab_id);
            if ($tags) {
                return $tags;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabPrograms($request)
    {
        try {
            $get_lab_programs = $this->labRepository->getLabPrograms($request);
            if ($get_lab_programs) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function genrateReportExcel($request)
    {
        try {
            $genrate_report_excel = $this->labRepository->genrateReportExcel($request);
            if ($genrate_report_excel) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function genrateReportPdf($request)
    {
        try {
            $genrate_report_pdf = $this->labRepository->genrateReportPdf($request);
            if ($genrate_report_pdf) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function likeUnlike($request)
    {
        try {
            $like_unlike = $this->labRepository->likeUnlike($request);
            if ($like_unlike) {
                return $like_unlike;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function followUnfollow(LabfollowRequest $request)
    {
        try {
            $follow_unfollow = $this->labRepository->followUnfollow($request);
            if ($follow_unfollow) {
                return $follow_unfollow;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function joinLab(LabJoinRequest $request)
    {
        try {
            $join_unjoin = $this->labRepository->joinLab($request);
            if ($join_unjoin) {
                return $join_unjoin;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function share(Request $request)
    {
        try {
            $share = $this->labRepository->share($request->id);
            if ($share) {
                return $share;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
