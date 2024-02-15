<?php

namespace App\Http\Controllers\Api\Public\Skill;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Skill\SkillRepository;
use Illuminate\Http\Request;
use App\Http\Resources\Public\Skill\SkillResource;
class SkillController extends AppBaseController
{
    private $skillRepository;
    public function __construct(SkillRepository $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    public function index(Request $request){
        try {
            $skillList = $this->skillRepository->index($request->language,$request->search,$skillId = null);

            if ($skillList) {
                $response = [
                    'total_count'  => $skillList->total(),
                    'per_page'     => $skillList->perPage(),
                    'count'        => $skillList->count(),
                    'current_page' => $skillList->currentPage(),
                    'total_pages'  => $skillList->lastPage(),
                    'list'         => SkillResource::collection($skillList),
                ];
                return $this->sendResponse($response, __('responses.found_skill_list'));
            }
        }catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function getMySkills(Request $request, $skillId = null)
    {
        try {
            if ($skillId !== null) {
                $skillList = $this->skillRepository->getSkillBasedOnId($skillId);

            } else {
                $skillList = $this->skillRepository->getMySkills($request->language, $request->search);

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
                } else {
                    $response = $resourceClass::make($skillList);
                }
                return $this->sendResponse($response, __('responses.skills_list'));
            }
            return $this->sendError(__('responses.skills_list_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
