<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use HiFolks\RandoPhp\Randomize;

class LabService
{
    public function createLab($request, $upload_cover_image)
    {
        $status = config('constants.lab_status.draft');
        switch($request->status) {
            case 'draft':
                $status = config('constants.lab_status.draft');
                break;
            case 'publish':
                $status = config('constants.lab_status.publish');
                break;
            case 'archive':
                $status = config('constants.lab_status.archive');
                break;
            default:
                $status = config('constants.lab_status.draft');
                break;
        }
        $privacy = config('constants.lab_privacy.no');
        switch($request->privacy) {
            case 'yes':
                $privacy = config('constants.lab_privacy.yes');
                break;
            case 'no':
                $privacy = config('constants.lab_privacy.no');
                break;
            default:
                $privacy = config('constants.lab_privacy.yes');
                break;
        }
        $model = new Lab();
        $slug = UtilityHelper::generateSlug($request->title, $model);

        $lab = new Lab();
        $lab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
        $lab->language = $request->language;
        $lab->user_id = auth()->user()->id;
        $lab->organization_id = $request->organization_id;
        $lab->category_id = $request->category_id;

        $lab->slug = $slug;
        $lab->title = $request->title;
        $lab->description = $request->description;
        $lab->privacy = $privacy;

        $lab->media_type = 'image';
        $lab->media = $upload_cover_image;

        $lab->status = $status;

        $lab->total_share = 0;

        $lab->is_auto_created = '0';

        $lab->is_resource_sequential = ($request->is_resource_sequential == 'yes') ? '1' : '0';
        $lab->is_sequential = ($request->is_sequential == 'yes') ? '1' : '0';
        $lab->is_achievement_enabled = ($request->is_achievement_enabled == 'yes') ? '1' : '0';
        $lab->is_notification_enabled = ($request->is_notification_enabled == 'yes') ? '1' : '0';
        $lab->is_verified = '0';
        $lab->save();

        return $lab;
    }

    public function uploadCoverImage($image)
    {
        try {
            $uploadLabCoverImage = FileUploadHelper::uploadImageToS3($image, 'lab');
            if ($uploadLabCoverImage == false) {
                return false;
            }

            return $uploadLabCoverImage;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabList($request)
    {
        try {
            $labList = Lab::select();
            $labList = $this->filterLabList($labList, $request);

            return $labList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterLabList($labList, $request)
    {
        try {
            if (isset($request->privacy) && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'yes':
                        $privacy = config('constants.lab_privacy.yes');
                        break;
                    case 'no':
                        $privacy = config('constants.lab_privacy.no');
                        break;
                    default:
                        $privacy = config('constants.lab_privacy.no');
                }
                $labList = $labList->where('privacy', $privacy);
            }
            $labList = $labList->get();

            return $labList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabDetails($slug)
    {
        try {
            $labDetails = Lab::where('slug', $slug)->first();
            if ($labDetails) {
                return $labDetails;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checklabSlug = Lab::where('slug', $slug)->first();
            if ($checklabSlug) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

public function checkNameExistsOrNot($title)
{
    try {
        $checklabName = Lab::where('title', $title)->first();
        if ($checklabName) {
            return true;
        }

        return false;
    } catch (\Exception $e) {
        return false;
    }
}
}
