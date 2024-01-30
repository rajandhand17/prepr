<?php

namespace App\Http\Controllers\Api\Manage\ChallengeTemplate;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\ChallengeTemplate\ChallengeTemplateResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateRepository;
use Exception;
use Illuminate\Http\Request;

class ChallengeTemplateController extends AppBaseController
{
    private ChallengeTemplateRepository  $challengeTemplateRepository;
    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeTemplateRepository $challengeTemplateRepository, ChallengeRepository $challengeRepository)
    {
        $this->challengeTemplateRepository = $challengeTemplateRepository;
        $this->challengeRepository = $challengeRepository;
    }

    public function index(Request $request)
    {
        try {
            $challengeTemplate = $this->challengeTemplateRepository->getChallengeTemplateList($request);
            if ($challengeTemplate) {
                $response = [
                    'total_count'  => $challengeTemplate->total(),
                    'per_page'     => $challengeTemplate->perPage(),
                    'count'        => $challengeTemplate->count(),
                    'current_page' => $challengeTemplate->currentPage(),
                    'total_pages'  => $challengeTemplate->lastPage(),
                    'list'         => ChallengeTemplateResource::collection($challengeTemplate),
                ];

                return $this->sendResponse($response, __('responses.found_challenge_templates_list'));
            }

            return $this->sendError(__('responses.not_found_challenge_templates_list'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addChallengeToTemplate($slug)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $addChallengeTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($checkComponentBasedOnSlug->id);
            if ($addChallengeTemplate != false) {
                return $this->sendResponse(ChallengeTemplateResource::make($addChallengeTemplate), __('responses.challenge_clone_success'), 200);
            }

            return $this->sendError(__('responses.challenge_clone_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challengeTemplate = $this->challengeTemplateRepository->getChallengeTemplateBasedOnSlug($slug);
            if ($challengeTemplate) {
                return $this->sendResponse(ChallengeTemplateResource::make($challengeTemplate), __('responses.found_challenge_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_challenge_detail'), 404);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
