<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use Exception;

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

    public static function createChallenge($request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            dd($request->all());
        } catch (Exception $th) {
            dd($th, 'In Service');

            return false;
        }
    }
}
