<?php

namespace App\Services\Maestro\Challenge;

use App\Models\Organization;
use App\Models\Challenge;
use App\Models\User;
use App\Models\Language;
use App\Models\ChallengeRequirement;
use App\Models\ChallengeTimelines;
use App\Helpers\UtilityHelper;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Models\Category;

class ChallengeService
{
    public static function getChallengeList()
    {
        try {
            return Challenge::latest();
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getLanguage()
    {
        try {
            $language = Language::where(['status' => 1])->pluck('name', 'iso');
            if ($language != null) {
                return $language;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function createChallenge($request)
    {
        try {

            if ($request->file('cover_image')) {
                $pathsArray = config('s3-upload-path');
                $file = $request->file('cover_image');
                $image_contents_cover = fopen($file->getRealPath(), 'rb');
                $coverImage = $pathsArray['resource_module'].time().'.webp';
                Storage::disk('s3')->put($coverImage, $image_contents_cover);
            } else {
                $coverImage = null;
            }

            $model = new Challenge();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $challenge = new Challenge();
            $challenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challenge->language = $request->language;
            $challenge->slug = $slug;
            $challenge->user_id = $request->user_id;
            $challenge->organization_id = $request->organization_id;
            $challenge->category_id = $request->category;
            $challenge->duration_id = $request->duration;
            $challenge->level_id = $request->level;
            $challenge->title = $request->title;
            $challenge->description = $request->description;
            $challenge->is_open = $request->is_open;
            $challenge->status = $request->status;
            // $challenge->privacy = $challenge_privacy;
            $challenge->media_type = 'image';
            $challenge->media = $coverImage;
            $challenge->status = $request->status;
            $challenge->agreement = ($request->has('agreement')) ? $request->agreement : 'No Terms and Conditions.';
            $challenge->project_privacy = $request->project_privacy;
            if($challenge->save()){
                self::challengeRequirementsSave($request,$challenge);
                self::challengeTimelinesSave($request,$challenge);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function challengeRequirementsSave($request,$challenge)
    {
        try {
            $challengeRequirement = new ChallengeRequirement();
            $challengeRequirement->challenge_id = $challenge->id;
            $challengeRequirement->min_rank     = (int) !empty($request->min_rank) ? $request->min_rank : null;
            $challengeRequirement->min_points   = (int) !empty($request->min_points) ? $request->min_points : null;
            $challengeRequirement->project_submission_requirement_ids = ['2','3'];//$request->project_submission_requirement_ids;
            
            if($challengeRequirement->save()){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function challengeTimelinesSave($request,$challenge)
    {
        try {
            $challengeTimeLines = new ChallengeTimelines();
            $challengeTimeLines->challenge_id  = $challenge->id;
            $challengeTimeLines->timeline_type = '1';
            $challengeTimeLines->open_call_date = $request->open_call_date;
            $challengeTimeLines->open_call_date_description = $request->open_call_date_description;
            $challengeTimeLines->last_call_date = $request->last_call_date;
            $challengeTimeLines->last_call_date_description = $request->last_call_date_description;
            $challengeTimeLines->application_deadline_date = $request->application_deadline_date;
            $challengeTimeLines->application_deadline_date_description = $request->application_deadline_date_description;
            $challengeTimeLines->submission_deadline_date = $request->submission_deadline_date;
            $challengeTimeLines->submission_deadline_date_description = $request->submission_deadline_date_description;
            // $challengeTimeLines->challenge_duration = $request->length;
            if($challengeTimeLines->save()){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function deleteChallenge($id)
    {
        try {
            $challenge = Challenge::find($id);
            if (!empty($challenge)) {
                return $challenge->delete();
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getChallengeById($id)
    {
        try {
            $challenge = Challenge::findOrFail($id);
            if ($challenge) {
                return $challenge;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function updateChallengeById($id, $request)
    {
        try {
            $challenge = Challenge::find($id);
            if (!empty($challenge)) {
                    // if ($request->file('cover_image')) {
                    //     $pathsArray = config('s3-upload-path');
                    //     $file = $request->file('cover_image');
                    //     $image_contents_cover = fopen($file->getRealPath(), 'rb');
                    //     $webp_path_cover = $pathsArray['resource_module'].time().'.webp';
                    //     Storage::disk('s3')->put($webp_path_cover, $image_contents_cover);
                    // } else {
                    //     $webp_path_cover    = $challenge->media;
                    // }
                    $challenge->title = $request->title;
                    $challenge->user_id = $request->user_id;
                    $challenge->organization_id = $request->organization_id;
                    $challenge->category_id = 11;//$request->category_id;
                    $challenge->duration_id = 11;//$request->duration_id;
                    $challenge->level_id = 11;//$request->level_id;
                    $challenge->description = $request->description;
                    $challenge->is_open = $request->is_open;
                    $challenge->status = $request->status;
                if ($challenge->save()) {
                    return true;
                }
                return false;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
