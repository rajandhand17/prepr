<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengePath;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ChallengePathService
{
    public static function uploadChallengePathMedia($image)
    {
        try {
            $upload_challenge_path_cover_image = FileUploadHelper::uploadImageToS3($image, 'challenge_path');
            if ($upload_challenge_path_cover_image == false) {
                return false;
            }

            return $upload_challenge_path_cover_image;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengePath($cover_image, $request)
    {
        try {
            $privacy = config('constants.challenge_privacy.no');
            switch ($request->privacy) {
                case 'yes':
                    $privacy = config('constants.challenge_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.challenge_privacy.no');
                    break;
                default:
                    $privacy = config('constants.challenge_privacy.yes');
                    break;
            }

            $status = config('constants.challenge_status.draft');
            switch ($request->status) {
                case 'draft':
                    $status = config('constants.challenge_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.challenge_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.challenge_status.archive');
                    break;
                default:
                    $status = config('constants.challenge_status.draft');
                    break;
            }

            $is_achievement_enabled = config('constants.challenge_achievement_enable.no');
            switch ($request->is_achievement_enabled) {
                case 'yes':
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.yes');
                    break;
                case 'no':
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.no');
                    break;
                default:
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.yes');
                    break;
            }

            $is_auto_created = config('constants.challenge_auto_created.no');
            switch ($request->is_auto_created) {
                case 'yes':
                    $is_auto_created = config('constants.challenge_auto_created.yes');
                    break;
                case 'no':
                    $is_auto_created = config('constants.challenge_auto_created.no');
                    break;
                default:
                    $is_auto_created = config('constants.challenge_auto_created.yes');
                    break;
            }

            $is_sequential = config('constants.challenge_sequential.no');
            switch ($request->is_sequential) {
                case 'yes':
                    $is_sequential = config('constants.challenge_sequential.yes');
                    break;
                case 'no':
                    $is_sequential = config('constants.challenge_sequential.no');
                    break;
                default:
                    $is_sequential = config('constants.challenge_sequential.yes');
                    break;
            }

            $model = new ChallengePath();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);

            $challengePath = new ChallengePath();
            $challengePath->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challengePath->language = $request->language;
            $challengePath->slug = $slug;
            $challengePath->title = $request->title;
            $challengePath->description = $request->description;
            $challengePath->user_id = auth()->user()->id;
            $challengePath->organization_id = $organization->id;
            $challengePath->category_id = $request->category_id;
            $challengePath->duration_id = $request->duration_id;
            $challengePath->level_id = $request->level_id;
            $challengePath->media_type = 'image';
            $challengePath->media = $cover_image;
            $challengePath->privacy = $privacy;
            $challengePath->status = $status;
            $challengePath->is_achievement_enabled = $is_achievement_enabled;
            $challengePath->is_sequential = $is_sequential;
            $challengePath->is_auto_created = $is_auto_created;
            $challengePath->save();

            return $challengePath;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkSlug($slug)
    {
        try {
            return ChallengePath::where('slug', $slug)->first();
        } catch (Exception $e) {
            return false;
        }
    }
}
