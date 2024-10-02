<?php

use App\Http\Controllers\Maestro\AutoCreateTemplate\AutoCreateTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth-check']], function () {
    Route::get('auto-create', [AutoCreateTemplateController::class, 'index'])->name('auto-create.index');
    Route::post('auto-create-update', [AutoCreateTemplateController::class, 'createUpdate'])->name('createUpdate');
    Route::post('changePrivacyOfLab', [AutoCreateTemplateController::class, 'changePrivacyOfLab'])->name('changePrivacyOfLab');
    Route::post('changePrivacyOfChallenge', [AutoCreateTemplateController::class, 'changePrivacyOfChallenge'])->name('changePrivacyOfChallenge');
    Route::post('getCloneResult', [AutoCreateTemplateController::class, 'getCloneResult'])->name('getCloneResult');
    Route::post('fetchLabAndChallengeDetails', [AutoCreateTemplateController::class, 'fetchLabAndChallengeDetails'])->name('fetchLabAndChallengeDetails');
    Route::get('fetchLabList', [AutoCreateTemplateController::class, 'fetchLabList'])->name('fetchLabList');
    Route::get('fetchChallengeList', [AutoCreateTemplateController::class, 'fetchChallengeList'])->name('fetchChallengeList');
    Route::get('fetchChallengeGroupList', [AutoCreateTemplateController::class, 'fetchChallengeGroupList'])->name('fetchChallengeGroupList');
    Route::post('getPreSelectLabList', [AutoCreateTemplateController::class, 'getPreSelectLabList'])->name('getPreSelectLabList');
    Route::post('getPreSelectedChallengeList', [AutoCreateTemplateController::class, 'getPreSelectedChallengeList'])->name('getPreSelectedChallengeList');
    Route::post('getPreSelectLabGroupList', [AutoCreateTemplateController::class, 'getPreSelectLabGroupList'])->name('getPreSelectLabGroupList');
    Route::post('getPreSelectChallengeGroupList', [AutoCreateTemplateController::class, 'getPreSelectChallengeGroupList'])->name('getPreSelectChallengeGroupList');
    Route::get('fetchLabGroupList', [AutoCreateTemplateController::class, 'fetchLabGroupList'])->name('fetchLabGroupList');
});
