<?php

namespace App\Traits\Maestro\CloneLab;

use App\Models\Lab;
use App\Services\Maestro\LabAchievementService;
use App\Services\Maestro\LabAddressService;
use App\Services\Maestro\LabExternalLinksService;
use App\Services\Maestro\LabService;
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
            $labService = LabService::getList();
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
            // Getting Lab and related tables
            $lab = Lab::with('skills', 'address', 'tags', 'external_links', 'achievement')->where('id', $request->lab)->first();
            $createdLab = DB::transaction(function () use ($lab, $request) {
                $newLab = LabService::createCloneLab($lab, $request->organization);
                $labAddress = LabAddressService::createCloneLabAddress($lab->address, $newLab->id);
                $labSKillsGroupStack = LabSkillsGroupsStackService::createCloneLabSkillsGroupsStack($lab->skills, $newLab->id);
                $labTagGroupStack = LabTagsGroupsService::createCloneLabTagsGroups($lab->tags, $newLab->id);
                $labExternalLinks = LabExternalLinksService::createCloneLabExternalLinks($lab->external_links, $newLab->id);
                $createdLabAchievement = LabAchievementService::createCloneLabAchievement($lab->achievement, $newLab->id);

                return [
                    'lab'                    => $newLab,
                    'lab_address'            => $labAddress,
                    'lab_sKills_group_stack' => $labSKillsGroupStack,
                    'lab_tag_group_stack'    => $labTagGroupStack,
                    'lab_external_links'     => $labExternalLinks,
                    'lab_achievement'        => $createdLabAchievement,
                ];
            });
            // Checking all the tables records inserted successfully
            if ($createdLab['lab'] && $createdLab['lab_address'] && $createdLab['lab_sKills_group_stack']
                && $createdLab['lab_tag_group_stack'] && $createdLab['lab_external_links'] && $createdLab['lab_achievement']) {
                DB::commit();

                // Returning new created table details
                return $createdLab['lab'];
            }
            DB::rollBack();

            return false;
        } catch(Exception $e) {
            DB::rollback();

            return false;
        }
    }
}
