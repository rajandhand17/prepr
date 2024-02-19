<?php

namespace App\Http\Controllers\Api\Public\Skill;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Skill\SkillResource;
use App\Repositories\Api\Public\Skill\SkillRepository;
use Illuminate\Http\Request;

class SkillController extends AppBaseController
{
    private $skillRepository;

    public function __construct(SkillRepository $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    public function index(Request $request, $skillId = null)
    {
        try {
            $skillList = $this->skillRepository->index($request->language, $request->search,$request->sort_by, $skillId);
            if ($skillList) {
                $resource=SkillResource::class;
                if($skillId==null) {
                    $response = [
                        'total_count'  => $skillList->total(),
                        'per_page'     => $skillList->perPage(),
                        'count'        => $skillList->count(),
                        'current_page' => $skillList->currentPage(),
                        'total_pages'  => $skillList->lastPage(),
                        'list'         => $resource::collection($skillList),
                    ];
                    $message= __('responses.skills_list');
                }else{
                    $response=$resource::make($skillList);
                    $message= __('responses.skills_list_detailed');
                }
                return $this->sendResponse($response, $message);
            }

            return $this->sendError(__('responses.not_found_skill_list'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getMySkills(Request $request, $skillId = null)
    {
        try {
            if ($skillId !== null) {
                $skillList = $this->skillRepository->getSkillBasedOnId($skillId);
            } else {
                $skillList = $this->skillRepository->getMySkills($request->language, $request->search,$request->pinned);
            }
            if ($skillList) {
                $resourceClass = SkillResource::class;
                if ($skillId == null) {
                    $response = [
                        'total_count'  => $skillList->total(),
                        'per_page'     => $skillList->perPage(),
                        'count'        => $skillList->count(),
                        'current_page' => $skillList->currentPage(),
                        'total_pages'  => $skillList->lastPage(),
                        'list'         => $resourceClass::collection($skillList),
                    ];
                    $message= __('responses.skills_list');
                } else {
                    $response = $resourceClass::make($skillList);
                    $message= __('responses.skills_list_detailed');
                }
                return $this->sendResponse($response, $message);
            }

            return $this->sendError(__('responses.skills_list_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
