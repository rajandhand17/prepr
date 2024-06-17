<?php

namespace App\Http\Controllers\Api\Public\Skill;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Public\Skill\AddSkillPinnedRequest;
use App\Http\Requests\Public\Skill\AddSkillRequest;
use App\Http\Resources\Public\Skill\AddSkillResource;
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
            $skillList = $this->skillRepository->index($request->language, $request->search, $request->sort_by, $skillId);
            if ($skillList) {
                $resource = SkillResource::class;
                if ($skillId == null) {
                    $response = [
                        'total_count'  => $skillList->total(),
                        'per_page'     => $skillList->perPage(),
                        'count'        => $skillList->count(),
                        'current_page' => $skillList->currentPage(),
                        'total_pages'  => $skillList->lastPage(),
                        'list'         => $resource::collection($skillList),
                    ];
                    $message = __('responses.skills_list');
                } else {
                    $response = $resource::make($skillList);
                    $message = __('responses.skills_list_detailed');
                }

                return $this->sendResponse($response, $message);
            }

            return $this->sendError(__('responses.not_found_skill_list'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addSkillsWithPinned(AddSkillRequest $request)
    {
        try {
            $addSkills = $this->skillRepository->addSkills($request);
            if ($addSkills === 'already') {
                return $this->sendError(__('responses.already_added_skills'), 422);
            }
            if ($addSkills) {
                return $this->sendResponse(AddSkillResource::make($addSkills), __('responses.save_skills'));
            }

            return $this->sendError(__('responses.add_skills_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addSKillPinned(AddSkillPinnedRequest $request)
    {
        try {
            $addPinnedSKills = $this->skillRepository->addSkillPinned($request);
            if ($addPinnedSKills) {
                $message = $request->pinned == 'yes' ? __('responses.pinned_skills_successfully') : __('responses.pinned_skills_successfully_removed');

                return $this->sendResponse(AddSkillResource::make($addPinnedSKills), $message);
            }

            return $this->sendError(__('responses.pinned_skills_failed'), 400);
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
                $skillList = $this->skillRepository->getMySkills($request->language, $request->search, $request->pinned);
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
                    $message = __('responses.skills_list');
                } else {
                    $response = $resourceClass::make($skillList);
                    $message = __('responses.skills_list_detailed');
                }

                return $this->sendResponse($response, $message);
            }

            return $this->sendResponse([], __('responses.skills_list'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
