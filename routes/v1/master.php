<?php

use App\Http\Controllers\Api\Master\MasterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language'])->group(function () {
    Route::get('/categories', [MasterController::class, 'getCategories']);
    Route::get('/skills', [MasterController::class, 'getSkills']);
    Route::get('/tags', [MasterController::class, 'getTags']);
    Route::get('/tag-groups', [MasterController::class, 'getTagGroup']);
    Route::get('/industries', [MasterController::class, 'getProjectIndustries']);
    Route::get('/types', [MasterController::class, 'getProjectTypes']);
    Route::get('/stages', [MasterController::class, 'getProjectStages']);
    Route::get('/verticals', [MasterController::class, 'getProjectVerticals']);
    Route::get('/status', [MasterController::class, 'getProjectStatus']);
    Route::get('/links', [MasterController::class, 'getSocialLinks']);
    Route::get('/skill-groups', [MasterController::class, 'getSkillGroups']);
    Route::get('/skill-stacks', [MasterController::class, 'getSkillStacks']);
    Route::get('/ranks', [MasterController::class, 'getRanks']);
    Route::get('/project-submission-requirement', [MasterController::class, 'getProjectSubmissionRequirements']);
    Route::get('/achievement-condition-list', [MasterController::class, 'getAchievementConditionLists']);
    Route::get('/host', [MasterController::class, 'getHosts']);
    Route::get('/flexible-date-duration', [MasterController::class, 'getFlexibleDateDurations']);
    Route::get('/pitch-templates', [MasterController::class, 'getPitchTemplates']);
    Route::get('/lab-conditions', [MasterController::class, 'getLabConditions']);
    Route::get('/social-connect', [MasterController::class, 'getSocialConnect']);
    Route::get('/durations', [MasterController::class, 'getDurations']);
    Route::get('/levels', [MasterController::class, 'getLevels']);
    Route::get('/check-pitch-task', [MasterController::class, 'getChallengePitchTask']);
    Route::post('/create-sponsor/', [MasterController::class, 'createSponsor']);
    Route::get('/countries', [MasterController::class, 'getCountries']);
    Route::get('/challenge-announcement-recipient', [MasterController::class, 'getChallengeAnnouncementRecipient']);
    Route::get('/job-titles', [MasterController::class, 'getJobTitles']);
    Route::get('/business-challenge-tacklings', [MasterController::class, 'businessChallengeTackling']);
    Route::post('get-labs', [MasterController::class, 'getLabs'])->name('getLabs');
});
