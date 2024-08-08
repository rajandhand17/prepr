<?php

use App\Http\Controllers\Api\Public\AdvanceSearch\AdvanceSearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['language', 'auth:api'])->group(function () {
    Route::get('/labs', [AdvanceSearchController::class, 'searchLab']);
    Route::get('/lab-programs', [AdvanceSearchController::class, 'searchLabPrograms']);
    Route::get('/lab-marketplaces', [AdvanceSearchController::class, 'searchLabMarketplace']);
    Route::get('/challenges', [AdvanceSearchController::class, 'searchChallenges']);
    Route::get('/challenge-templates', [AdvanceSearchController::class, 'searchChallengeTemplates']);
    Route::get('/challenge-paths', [AdvanceSearchController::class, 'searchChallengePaths']);
    Route::get('/resource-modules', [AdvanceSearchController::class, 'searchResourceModules']);
    Route::get('/resource-collections', [AdvanceSearchController::class, 'searchResourceCollections']);
    Route::get('/resource-groups', [AdvanceSearchController::class, 'searchResourceGroups']);
    Route::get('/projects', [AdvanceSearchController::class, 'searchProjects']);
    Route::get('/organizations', [AdvanceSearchController::class, 'searchOrganization']);
    Route::get('/users', [AdvanceSearchController::class, 'searchUsers']);
});
