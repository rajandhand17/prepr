<?php

namespace App\Http\Controllers\Api\Manage\LabTemplate;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\LabTemplate\LabTemplateResource;
use App\Repositories\Api\Manage\Lab\LabRepository;
use App\Repositories\Api\Manage\LabTemplate\LabTemplateRepository;

class LabTemplateController extends AppBaseController
{
    private LabTemplateRepository $labTemplateRepository;
    private LabRepository $labRepository;

    public function __construct(LabRepository $labRepository, LabTemplateRepository $labTemplateRepository)
    {
        $this->labTemplateRepository = $labTemplateRepository;
        $this->labRepository = $labRepository;
    }

    public function createTemplate($slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if (!$lab) {
                return $this->sendError(__('responses.lab_slug_not_found'), 404);
            }
            $createdLabTemplate = $this->labTemplateRepository->createLabTemplate($slug, $lab);
            if ($createdLabTemplate) {
                return $this->sendResponse(LabTemplateResource::make($createdLabTemplate), __('responses.template_lab_stored_success'), 200);
            }

            return $this->sendError(__('responses.template_lab_stored_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug){
        try {
            $labTemplate = $this->labTemplateRepository->getLabTemplateBasedOnSlug($slug);
            if ($labTemplate) {
                return $this->sendResponse(LabTemplateResource::make($labTemplate), __('responses.template_lab_stored_success'), 200);
            }
        }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
