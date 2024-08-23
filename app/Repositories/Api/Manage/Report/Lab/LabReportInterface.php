<?php

namespace App\Repositories\Api\Manage\Report\Lab;

use App\Models\Lab;
use App\Models\LabProgram;

interface LabReportInterface
{
    public function getLabEngagement(Lab $lab): false|array;

    public function labMemberProgress(Lab $lab): array|false;

    public function labMemberActivity(Lab $lab): array|false;

    public function getPaginatedChallenges(Lab $lab): false|array;

    public function getPaginatedResourceModules(Lab $lab): false|array;

    public function getPaginatedResourceCollections(Lab $lab): false|array;

    public function getPaginatedResourceGroups(Lab $lab): false|array;

    public function getPaginatedChallengePaths(Lab $lab): false|array;

    public function getPaginatedAchievements(Lab $lab): false|array;

    public function getPaginatedMembers($lab): false|array;

    public function getLabProgramMemberProgress(LabProgram $labProgram): array|false;
}
