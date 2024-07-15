<?php

namespace App\Traits\Maestro\CloneLab;

use App\Models\Lab;
use App\Services\Maestro\LabAchievementService;
use App\Services\Maestro\LabExternalLinksService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\LabAddressService;
use App\Services\Maestro\LabSkillsGroupsStackService;
use App\Services\Maestro\LabTagsGroupsService;
use Exception;
use Illuminate\Support\Facades\DB;

trait CloneLabTrait
{
    protected $labService;
    public function __construct(LabService $labService)
    {
        $this->labService = $labService;
    }
    public function getAllLabs()
    {
        try {
            $labService =LabService::getList();
            if ($labService) {
                return $labService;
            }
            return false;
        } catch(Exception $e) {
            return false;
        }
    }
    public function createLab($request)
    {
        try {
            $lab=Lab::with("skills","address","tags","external_links","achievement")->where('id',$request->lab)->first();
            $createdLab = DB::transaction(function () use ($lab, $request) {
                $newLab = LabService::createLab($lab, $request->organization);
                $labAddress = LabAddressService::createLabAddress($lab->address, $newLab->id);
                $labSKillsGroupStack = LabSkillsGroupsStackService::createLabSkillsGroupsStack($lab->skills, $newLab->id);
                $labTagGroupStack = LabTagsGroupsService::createLabTagsGroups($lab->tags, $newLab->id);
                $labExternalLinks = LabExternalLinksService::createLabExternalLinks($lab->external_links, $newLab->id);
                $createdLabAchievement = LabAchievementService::createLabAchievement($lab->achievement, $newLab->id);
                return [
                    'lab'                    => $newLab,
                    'lab_address'            => $labAddress,
                    'lab_sKills_group_stack' => $labSKillsGroupStack,
                    'lab_tag_group_stack'    => $labTagGroupStack,
                    'lab_external_links'     => $labExternalLinks,
                    'lab_achievement'        => $createdLabAchievement,
                ];
            });
            if ($createdLab['lab'] && $createdLab['lab_address'] && $createdLab['lab_sKills_group_stack']
                && $createdLab['lab_tag_group_stack'] && $createdLab['lab_external_links'] && $createdLab['lab_achievement']) {
                DB::commit();

                return $createdLab['lab'];
            }
            DB::rollBack();

            return false;
        } catch(Exception $e) {
            DB::rollback();
            dd($e);
            return false;
        }
    }
}
