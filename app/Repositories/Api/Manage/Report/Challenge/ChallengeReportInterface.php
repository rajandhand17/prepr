<?php

namespace App\Repositories\Api\Manage\Report\Challenge;

use App\Models\Challenge;
use App\Models\ChallengePath;

interface ChallengeReportInterface
{
    public function getChallengeMemberProgress(Challenge $challenge): false|array;

    public function getChallengeEngagement(Challenge|null $challenge): false|array;

    public function getPaginatedLabs(Challenge $challenge): array|false;

    public function getPaginatedLabPrograms(Challenge $challenge): array|false;

    public function getPaginatedResourceModules(Challenge $challenge): array|false;

    public function getPaginatedResourceCollections(Challenge $challenge): false|array;

    public function getPaginatedResourceGroups(Challenge $challenge): false|array;

    public function getPaginatedAchievements(Challenge $challenge): false|array;

    public function getPaginatedMembers(Challenge $challenge): false|array;

    public function challengeMemberActivity(Challenge $challenge): array|false;

    public function getPaginatedAssessments(Challenge $challenge): false|array;

    public function getChallengeAssessmentDetail(Challenge $challenge, $project_id): false|array;

    public function getChallengeAssociatedProjects(Challenge $challenge): false|array;

    public function getChallengePathMemberProgress(ChallengePath $challengePath): false|array;
}
