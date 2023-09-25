<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ChallengeService
{
    public static function uploadChallengeCoverImage($image)
    {
        try {
            $upload_challenge_cover_image = FileUploadHelper::uploadImageToS3($image, 'challenge');
            if ($upload_challenge_cover_image == false) {
                return false;
            }

            return $upload_challenge_cover_image;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function createChallenge($request, $upload_cover_image)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            $status = config('constants.challenge_status.draft');
            switch ($request->request_type) {
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

            $challenge_privacy = config('constants.challenge_privacy.no');
            switch ($request->privacy) {
                case 'yes':
                    $challenge_privacy = config('constants.challenge_privacy.yes');
                    break;
                case 'no':
                    $challenge_privacy = config('constants.challenge_privacy.no');
                    break;
                default:
                    $challenge_privacy = config('constants.challenge_privacy.yes');
                    break;
            }

            $project_privacy = config('constants.challenge_privacy.no');
            switch ($request->project_privacy) {
                case 'yes':
                    $project_privacy = config('constants.challenge_privacy.yes');
                    break;
                case 'no':
                    $project_privacy = config('constants.challenge_privacy.no');
                    break;
                default:
                    $project_privacy = config('constants.challenge_privacy.yes');
                    break;
            }

            $is_notification_enabled = config('constants.challenge_notification_enabled.no');
            switch ($request->is_notification_enabled) {
                case 'yes':
                    $is_notification_enabled = config('constants.challenge_notification_enabled.yes');
                    break;
                case 'no':
                    $is_notification_enabled = config('constants.challenge_notification_enabled.no');
                    break;
                default:
                    $is_notification_enabled = config('constants.challenge_notification_enabled.yes');
                    break;
            }

            $is_open = config('constants.challenge_open_close.no');
            switch ($request->is_open) {
                case 'yes':
                    $is_open = config('constants.challenge_open_close.yes');
                    break;
                case 'no':
                    $is_open = config('constants.challenge_open_close.no');
                    break;
                default:
                    $is_open = config('constants.challenge_open_close.yes');
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

            $model = new Challenge();
            $slug = UtilityHelper::generateSlug($request->title, $model);

            $challenge = new Challenge();
            $challenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challenge->language = $request->language;
            $challenge->slug = $slug;
            $challenge->user_id = auth()->user()->id;
            $challenge->organization_id = $organization->id;
            $challenge->category_id = $request->category_id;
            $challenge->duration_id = $request->duration_id;
            $challenge->level_id = $request->level_id;
            $challenge->title = $request->title;
            $challenge->description = $request->description;
            $challenge->privacy = $challenge_privacy;
            $challenge->media_type = 'image';
            $challenge->media = $upload_cover_image;
            $challenge->status = $status;
            $challenge->source_link = $request->source_link;
            $challenge->agreement = $request->agreement;
            $challenge->is_notification_enabled = $is_notification_enabled;
            $challenge->project_privacy = $project_privacy;
            $challenge->is_open = $is_open;
            $challenge->is_auto_created = $is_auto_created;
            $challenge->save();

            return $challenge;
        } catch (Exception $th) {
            return false;
        }
    }

    public static function getChallengeBasedOnSlug($slug)
    {
        try {
            return Challenge::where('slug', $slug)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteChallenge($challenge_id)
    {
        try {
            Challenge::find($challenge_id)->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
