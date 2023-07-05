<?php

namespace App\Repositories\Api\LabAcheivement;

use App\Services\LabAcheivementService;

class LabAcheivementRepository implements LabAcheivementInterface
{
    private $LabAcheivementService;
    private $memberManagementService;

    public function __construct(LabAcheivementService $LabAcheivementService)
    {
        $this->LabAcheivementService = $LabAcheivementService;
    }

    public function uploadAcheivementImage($image)
    {
        try {
            return $this->LabAcheivementService->uploadAcheivementImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateAcheivementImage($image)
    {
        try {
            return $this->LabAcheivementService->updateAcheivementImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }
}
