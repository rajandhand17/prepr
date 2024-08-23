<?php

use App\Http\Controllers\Api\Manage\Report\ChallengePathReportController;
use App\Http\Controllers\Api\Manage\Report\ChallengeReportController;
use App\Http\Controllers\Api\Manage\Report\LabProgramReportController;
use App\Http\Controllers\Api\Manage\Report\LabReportController;
use App\Http\Controllers\Api\Manage\Report\OrganizationReportController;
use App\Http\Controllers\Api\Manage\Report\ResourceReportController;
use Illuminate\Support\Facades\Route;

Route::namespace('report')->middleware(['auth:api'])->group(function () {
    /**
     * LAB REPORT APIS.
     */
    Route::prefix('lab')->group(function () {
        Route::get('{slug}', [LabReportController::class, 'details']);
        Route::get('{slug}/engagement', [LabReportController::class, 'labEngagement']);
        Route::get('{slug}/member-progress', [LabReportController::class, 'labMemberProgress']);
        Route::get('{slug}/challenges', [LabReportController::class, 'labChallenges']);
        Route::get('{slug}/resource-modules', [LabReportController::class, 'labResourceModules']);
        Route::get('{slug}/resource-collections', [LabReportController::class, 'labResourceCollections']);
        Route::get('{slug}/resource-groups', [LabReportController::class, 'labResourceGroups']);
        Route::get('{slug}/challenge-paths', [LabReportController::class, 'labChallengePaths']);
        Route::get('{slug}/achievements', [LabReportController::class, 'labAchievements']);
        Route::get('{slug}/members', [LabReportController::class, 'labMembers']);
        Route::get('{slug}/member-activity', [LabReportController::class, 'labMemberActivity']);
        Route::get('{slug}/export', [LabReportController::class, 'labExport']);
    });

    /**
     * LAB Program REPORT APIS.
     */
    Route::prefix('lab-program')->group(function () {
        Route::get('{slug}/member-progress', [LabProgramReportController::class, 'labProgramMemberProgress']);
    });

    /**
     * LAB Program REPORT APIS.
     */
    Route::prefix('challenge-path')->group(function () {
        Route::get('{slug}/member-progress', [ChallengePathReportController::class, 'challengePathMemberProgress']);
    });

    /**
     * CHALLENGE REPORT APIS.
     */
    Route::prefix('challenge')->group(function () {
        Route::get('{slug}', [ChallengeReportController::class, 'details']);
        Route::get('{slug}/member-progress', [ChallengeReportController::class, 'challengeMemberProgress']);
        Route::get('{slug}/engagement', [ChallengeReportController::class, 'challengeEngagement']);
        Route::get('{slug}/achievements', [ChallengeReportController::class, 'challengeAchievements']);
        Route::get('{slug}/members', [ChallengeReportController::class, 'challengeMembers']);
        Route::get('{slug}/associated-projects', [ChallengeReportController::class, 'challengeAssociatedProjects']);
        Route::get('{slug}/assessments', [ChallengeReportController::class, 'challengeAssessments']);
        Route::get('{slug}/assessments/{project_slug}', [ChallengeReportController::class, 'challengeAssessmentDetail']);
        Route::get('{slug}/member-activity', [ChallengeReportController::class, 'challengeMemberActivity']);
        Route::get('{slug}/export', [ChallengeReportController::class, 'challengeExport']);

        /** COMPONENTS */
        Route::get('{slug}/labs', [ChallengeReportController::class, 'challengeLabs']);
        Route::get('{slug}/lab-programs', [ChallengeReportController::class, 'challengeLabPrograms']);
        Route::get('{slug}/resource-modules', [ChallengeReportController::class, 'challengeResourceModules']);
        Route::get('{slug}/resource-collections', [ChallengeReportController::class, 'challengeResourceCollection']);
        Route::get('{slug}/resource-groups', [ChallengeReportController::class, 'challengeResourceGroups']);
    });

    /**
     * RESOURCE MODULE REPORT API.
     */
    Route::prefix('resource-module')->group(function () {
        Route::get('{slug}/member-progress', [ResourceReportController::class, 'getResourceModuleMemberProgress']);
    });

    /**
     * RESOURCE GROUP REPORT API.
     */
    Route::prefix('resource-group')->group(function () {
        Route::get('{slug}/member-progress', [ResourceReportController::class, 'getResourceGroupMemberProgress']);
    });

    /**
     * RESOURCE COLLECTION REPORT API.
     */
    Route::prefix('resource-collection')->group(function () {
        Route::get('{slug}/member-progress', [ResourceReportController::class, 'getResourceCollectionMemberProgress']);
    });

    /**
     * ORGANIZATION REPORT API.
     */
    Route::prefix('organization')->group(function () {
        Route::get('{slug}/details', [OrganizationReportController::class, 'details']);
        Route::get('{slug}/engagement', [OrganizationReportController::class, 'organizationEngagement']);
        Route::get('{slug}/organization-members', [OrganizationReportController::class, 'organizationMembers']);
        Route::get('{slug}/challenges', [OrganizationReportController::class, 'organizationChallenges']);
        Route::get('{slug}/challenge-paths', [OrganizationReportController::class, 'organizationChallengePath']);
        Route::get('{slug}/labs', [OrganizationReportController::class, 'organizationLabs']);
        Route::get('{slug}/lab-programs', [OrganizationReportController::class, 'organizationLabPrograms']);
        Route::get('{slug}/resource-modules', [OrganizationReportController::class, 'organizationResourceModules']);
        Route::get('{slug}/resource-collections', [OrganizationReportController::class, 'organizationResourceCollections']);
        Route::get('{slug}/resource-groups', [OrganizationReportController::class, 'organizationResourceGroups']);
        Route::get('{slug}/member-activity', [OrganizationReportController::class, 'organizationMemberActivity']);
        Route::get('{slug}/export', [OrganizationReportController::class, 'organizationExport']);
    });
});
